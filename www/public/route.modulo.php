<?php

$app->group('/modulos', function() {
    $this->get('/modulo', function($req, $res, $params) {
        $sth = $this->pdo->prepare("select *, IFNULL((SELECT A.TX_MODULO FROM MODULO A WHERE A.ID_SUBGRUPO = B.ID_MODULO),'-') AS TX_DEP from MODULO B ORDER BY B.ID_SUBGRUPO");
        $sth->execute();
        return $res->withStatus(200)->withJson($sth->fetchAll());
    });

    $this->post('/modulo', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("INSERT INTO SPMM.MODULO (ID_SISTEMA,ID_SUBGRUPO,TX_MODULO) VALUES (:ID_SISTEMA, :ID_SUBGRUPO, :TX_MODULO)");
        $sth->bindParam(':ID_SISTEMA', $ID_SISTEMA);
        $sth->bindParam(':ID_SUBGRUPO', $ID_SUBGRUPO);
        $sth->bindParam(':TX_MODULO', $TX_MODULO);
        if ($sth->execute()) {
            $sth = $this->pdo->prepare("SELECT LAST_INSERT_ID() AS ID");
            $sth->execute();

            return $res->withStatus(200)->withJson([
                'ID_MODULO' => $sth->fetch()['id']
            ]);
        }
    });

    $this->put('/modulo/{id:[0-9]+}', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("UPDATE SPMM.MODULO SET ID_SISTEMA=:ID_SISTEMA,TX_MODULO=:TX_MODULO WHERE ID_MODULO = :ID_MODULO");
        $sth->bindParam(':ID_SISTEMA', $ID_SISTEMA);
        $sth->bindParam(':TX_MODULO', $TX_MODULO);
        $sth->bindParam(':ID_MODULO', $params['id']);
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_MODULO' => $params['id'],
                'TX_MODULO' => $TX_MODULO
            ]); 
        }
    });

    $this->delete('/modulo/{id:[0-9]+}', function($req, $res, $params) {
        $sth = $this->pdo->prepare("DELETE FROM SPMM.MODULO WHERE ID_MODULO = :ID_MODULO");
        $sth->bindParam(':ID_MODULO', $params['id']);

        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_MODULO' => $params['id']
            ]);
        }
    });

    $this->delete('/modulo/all', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.MODULO ORDER BY TX_MODULO");
        $modulos = $sth->fetchAll();

        $sth = $this->pdo->prepare("DELETE FROM SPMM.MODULO");
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'message' => 'Todos os modulos foram deletados: ' . implode(',', $modulos)    
            ]);
        }
    });  
});