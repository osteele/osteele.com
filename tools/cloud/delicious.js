var Delicious = {};

Delicious.Data = function(username, password) {
  this.db = openDatabase("Expialidocious", "1.0", "Expialidocious", 200000);
};

(function() {
  var tTagIds = {};
  var gPending = {};

  var methods = {
  reset:function() {
      dropTables();
    },

  populate:function() {
      $.ajax({
        url: 'posts.xml', //'proxy.php',
        method: 'GET',
        dataType: 'text',
        authentication: { username: 'user', password: 'xxxxxx' },
        success: function(s) { populateFrom(parseXML(s)) },
        error: function(a, b, c) { console.info('error', a, b, c) }
      });
    },

  withPostCount:function(k, ek) {
      this.db.transaction(function(tx) {
          tx.executeSql("SELECT COUNT(*) AS count FROM posts", [], function(tx, rs) {
              //tx.executeSql("DELETE FROM posts");
              //tx.executeSql("DELETE FROM tags");
              var count = rs.rows.item(0).count;
              k(count);
            }, function(tx, err) {
              ek && ek();
            });
        });
    },

  withPostTimes:function(options, k) {
      var sql = "SELECT time FROM posts ORDER BY time", params = [];
      if (options.tagName) {
        sql = "SELECT time from posts JOIN post_tags ON posts.id=post_tags.post_id JOIN tags ON tags.id=post_tags.tag_id WHERE tags.name=? ORDER BY time";
        params = [options.tagName];
      }
      var times = [];
      $db(this.db).each(sql, params,
                        function(item) { times.push(item.time) },
                        function() { k(times) });
    },

  withPostsByTag:function(options, ik, fk) {
      this.db.transaction(function(tx) {
          tx.executeSql("SELECT DISTINCT * from posts JOIN post_tags ON posts.id=post_tags.post_id JOIN tags ON tags.id=post_tags.tag_id WHERE tags.name=?", [options.tagName], function(tx, rs) {
              for (var i = 0; i < rs.rows.length; i++)
                ik(rs.rows.item(i));
              fk();
            });
        });
    },

  withTags:function(ik, fk) {
      this.db.transaction(function (tx) {
          tx.executeSql("SELECT *, count(post_id) AS count, tags.name AS name FROM post_tags JOIN tags ON tags.id=post_tags.tag_id GROUP BY tag_id", [], function(tx, rs) {
              console.info(rs.rows.length, 'tags');
              for (var i = 0; i < rs.rows.length; i++)
                ik(rs.rows.item(i), i);
              fk && fk();
            });
        });
    }
  };

  function createTables(tx, k) {
    console.info('creating database');
    txRunStatements(tx,
                    ["CREATE TABLE posts (id INTEGER PRIMARY KEY, href TEXT UNIQUE, hash TEXT, meta TEXT, description TEXT, time INTEGER, tags TEXT)",
                     "CREATE TABLE tags (id INTEGER PRIMARY KEY, name TEXT UNIQUE)",
                     "CREATE TABLE post_tags (post_id INTEGER, tag_id INTEGER)",
                     "CREATE INDEX post_tags_post_index ON post_tags (post_id)",
                     "CREATE INDEX post_tags_tag_index ON post_tags (tag_id)"
                     ],
                    k,
                    showError('creating table'));
  }
  $.extend(Delicious.Data.prototype, methods);

  function dropTables() {
    runStatements(["DROP TABLE posts",
                   "DROP TABLE tags",
                   "DROP TABLE post_tags"]);
  }

  function populateFrom(doc) {
    console.info('parsing', $('post', doc).length, 'posts');
    //console.info($('post #1 =', doc)[0]);
    var fields = "href hash description tag time meta".split(/\s+/);
    var count = 0;
    this.db.transaction(function (tx) {
        tx.executeSql("DELETE FROM posts");
        $('post', doc).each(function() {
            //if (++count > 2) return;
            var post = this;
            var obj = {};
            $(fields).each(function(){obj[this] = post.getAttribute(this)});
            obj.time = parseDate(obj.time).getTime();
            tx.executeSql("INSERT INTO posts (href, hash, meta, description, time, tags) VALUES (?, ?, ?, ?, ?, ?)",
                          [obj.href, obj.hash, obj.meta, obj.description, obj.time, obj.tag],
                          function(tx, rs) {
                            var post_id = rs.insertId;
                            $(obj.tag.split(/\s+/)).each(function() {
                                with_tag_id(tx, this, function(tx, tag_id) {
                                    tx.executeSql("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)",
                                                  [post_id, tag_id],
                                                  function(){},
                                                  showError('insert post<->tag association'))
                                      });
                              });
                          }, showError('insert post'));
          })}, showError('populate'));

    function with_tag_id(tx, tag_name, fn, no_retry) {
      if (tag_name in tag_name)
        fn(tx, tTagIds[tag_name]);
      else if (tag_name in gPending)
        gPending[tag_name].push(fn);
      else {
        gPending[tag_name] = [];
        tx.executeSql("SELECT id FROM tags WHERE name=?", [tag_name],
                      function(tx, rs) {
                        if (rs.rows.length)
                          doit(tx, rs.rows.item(0).id);
                        else
                          tx.executeSql("INSERT INTO tags (name) VALUES (?)", [tag_name],
                                        function(tx, rs) {
                                          doit(tx, rs.insertId);
                                        }, showError('insert tag ' + tag_name));
                      },
                      !no_retry ? function(tx) {
                        with_tag_id(tx, tag_name, fn, true)
                          } : showError('select tag id'));
        function doit(tx, tag_id) {
          tTagIds[tag_name] = tag_id;
          fn(tx, tag_id);
          var pending = gPending[tag_name];
          delete gPending[tag_name];
          while (pending.length)
            pending.shift()(tx, tag_id);
        }
      }
    }
  }
 })();
