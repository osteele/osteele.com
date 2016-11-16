function runStatements(statements, k, err) {
//   k = k || function() {}
//   err = err || showError;
  return window.gDB.transaction(function(tx) {
      txRunStatements(tx, statements, k, err);
    });
}

function txRunStatements(tx, statements, k, err) {
  var stmt = statements[0], args = [];
  if (stmt instanceof Array) {
    args = stmt[1];
    stmt = stmt[0];
  }
  tx.executeSql(stmt, args,
                statements.length > 1 ? function(tx) {
                  txRunStatements(tx, statements.slice(1), k, err);
                } : k,
                err || showError(stmt));
}

function showError(msg, err) {
  if (arguments.length == 2)
    console.error('SQL error:', err.message);
  else
    return function(tx, err) {window.gLastSqlError=err;console.error('SQL error (' + msg + '):', (err||{}).message)};
}

function showRs(rs) {
  console.info(rs.rows.length);
  for (var i = 0; i < rs.rows.length; i++)
    console.info(JSON.stringify(rs.rows.item(i)));
}

function showQuery(stmt) {
  gDB.transaction(function (tx) {
      tx.executeSql(stmt, [], function(tx, rs) {
          showRs(rs);
        });
    }, showError);
}

function $db(db) {
  return {
  each: function(stmt, params, eachfn, k) {
      db.transaction(function (tx) {
          tx.executeSql(stmt, params, function(tx, rs) {
              for (var i = 0; i < rs.rows.length; i++)
                eachfn(rs.rows.item(i));
              k();
            });
        }, showError);
    }
  };
}
