(function () {
    
    if(typeof DB == "undefined") DB = {};
    var module = DB;
    
    var GEARS_COMPAT = false
    
    // Add Compatibility with the Gears DB
    if(typeof openDatabase == "undefined") {
        GEARS_COMPAT = true;
        openDatabase = function (name) {
            if(typeof google == "undefined" || !google.gears) {
                throw "Google Gears required."
            }

            var handle = google.gears.factory.create('beta.database');
            handle.open(name);
            var db = new module.HTML5DatabaseEmulator(handle)
            return db
        }
    }
    
    // Wrap the gears db db with something that looks like an html5 db
    
    DB.HTML5DatabaseEmulator = function (gearsDB) {
        this.gearsDB = gearsDB
    }

    DB.HTML5DatabaseEmulator.prototype = {
        transaction: function (func) {
            var tx = new module.HTML5TransactionEmulator(this)
            func(tx)
        }
    };
    
    DB.HTML5TransactionEmulator = function (database) {
        this.database = database
    }
    DB.HTML5TransactionEmulator.prototype = {
        executeSql: function (sql,args,onSuccess,onFailure) {
            var me = this;
            var resultSet;
            try {
                if(typeof console != "undefined") {
                    console.log("SQL: "+sql+" Args: "+args)
                }
                /// XXX run this inside a worker to really become async
                var rs = this.database.gearsDB.execute(sql, args);
                resultSet = new module.HTML5ResultSetEmulator(this.database, rs)
                
            } catch(e) {
                if(typeof console != "undefined") {
                    console.log(e + "\nSQL: "+sql + "\nArgs: "+args)
                }
                if(onFailure) {
                    onFailure(me, e)
                } else {
                    throw e
                }
            };
            if(resultSet) {
                if(onSuccess) {
                    onSuccess(me, resultSet)
                }
            }
        }
    };
    
    DB.HTML5ResultSetEmulator = function (database, resultSet) {
        this.database = database;
        this.resultSet = resultSet;
        this.insertId  = null;
        this.rowsAffected = null
        this.rows = null;
        
        var rs = this.resultSet;
        this.insertId = this.database.gearsDB.lastInsertRowId;
        var rows = [];
        rows.item = function (i) {
            return this[i]
        };
        var names = [];
        var fieldCount = rs.fieldCount();
        for(var i = 0; i < fieldCount; i++) {
            names.push(rs.fieldName(i))
        }
        while (rs.isValidRow()) {
            var row = {};
            row.length = fieldCount;
            for(var i = 0; i < names.length; i++) {
                var name = names[i];
                row[name] = rs.fieldByName(name)
            }
            for(var i = 0; i < fieldCount; i++) {
                row[i] = rs.field(i)
            }
            rows.push(row)
            rs.next();
        }
        rs.close();
        this.rows = rows
    }
    
    
    // Global vars, hidden from the outside :)
    var Database, TX;
    var ACTIVE_TRANSACTIONS = 0;
    var TRANSACTION_QUEUE   = [];
    
    // ORM.openDatabase - use this to open the database
    module.openDatabase = function (name, version, desc, size) {
        Database = openDatabase(name, version, desc, size)
    };
    
    var nextTransaction = function () {
        if(TRANSACTION_QUEUE.length > 0) {
            var txCallback = TRANSACTION_QUEUE.shift();
            module.transaction(txCallback)
        }
    };
    
    // ORM.transaction - use this to do a transaction
    // Sets current transaction use ORM.executeSql to execute SQL
    // With HTML5 it is guaranteed that transactions are serialized per window and database
    // For gears we build a transaction queue and execute it serialized
    module.transaction  = function (transactionCallback) {
        var me = this;
        if(typeof console != "undefined")
            console.log("Starting transaction ")
        Database.transaction(function (tx) {
            if(GEARS_COMPAT) {
                   
                if(ACTIVE_TRANSACTIONS > 0) { // only one transaction at a time
                    TRANSACTION_QUEUE.push(transactionCallback)
                    return
                }
                ACTIVE_TRANSACTIONS++
                TX = tx
                module.executeSql("BEGIN")
                try {
                    transactionCallback()
                } catch(e) {
                    module.executeSql("ROLLBACK");
                    ACTIVE_TRANSACTIONS--;
                    throw e
                };
                module.executeSql("COMMIT")
                ACTIVE_TRANSACTIONS--
                nextTransaction()
            } else {
                TX = tx
                transactionCallback()
            }
        })
    };
    
    // Execute Sql using the current transaction
    module.executeSql = function (sql, args, onSuccess, onError) {
        if(!GEARS_COMPAT && typeof console != "undefined") { // the gears layer does this anyway
            console.log("Executing SQL: "+sql+" Args: "+args)
        }
        TX.executeSql(
            sql, 
            args,
            function onExecuteSqlSuccess (tx, result) {
                if(onSuccess) {
                    onSuccess(result)
                }
            },
            function onExecuteSqlError (tx, error) {
                if(onError) {
                    onError(error)
                } else {
                    throw new Error(error)
                }
            })
    }
})()