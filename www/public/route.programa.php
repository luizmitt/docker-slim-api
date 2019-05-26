<?php

$app->group('/programas', function() {
    $this->get('/programa', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.PROGRAMA P INNER JOIN SPMM.MODULO M ON M.ID_MODULO = P.ID_MODULO INNER JOIN SISTEMA S ON S.ID_SISTEMA = M.ID_SISTEMA ORDER BY P.TX_PROGRAMA");
        $sth->execute();
        return $res->withStatus(200)->withJson($sth->fetchAll());
    });

    $this->post('/programa', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("INSERT INTO SPMM.PROGRAMA (ID_MODULO,TX_PROGRAMA) VALUES (:ID_MODULO, :TX_PROGRAMA)");
        $sth->bindParam(':ID_MODULO', $ID_MODULO);
        $sth->bindParam(':TX_PROGRAMA', $TX_PROGRAMA);
        if ($sth->execute()) {
            $sth = $this->pdo->prepare("SELECT LAST_INSERT_ID() AS ID");
            $sth->execute();

            return $res->withStatus(200)->withJson([
                'ID_PROGRAMA' => $sth->fetch()['id']
            ]);
        }
    });

    $this->put('/programa/{id:[0-9]+}', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("UPDATE SPMM.PROGRAMA SET TX_PROGRAMA=:TX_PROGRAMA WHERE ID_PROGRAMA = :ID_PROGRAMA");
        $sth->bindParam(':TX_PROGRAMA', $TX_PROGRAMA);
        $sth->bindParam(':ID_PROGRAMA', $params['id']);
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_PROGRAMA' => $params['id'],
                'TX_PROGRAMA' => $TX_PROGRAMA
            ]); 
        }
    });

    $this->delete('/programa/{id:[0-9]+}', function($req, $res, $params) {
        $sth = $this->pdo->prepare("DELETE FROM SPMM.PROGRAMA WHERE ID_PROGRAMA = :ID_PROGRAMA");
        $sth->bindParam(':ID_PROGRAMA', $params['id']);

        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_PROGRAMA' => $params['id']
            ]);
        }
    });

    $this->delete('/programa/all', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.PROGRAMA ORDER BY TX_PROGRAMA");
        $programas = $sth->fetchAll();

        $sth = $this->pdo->prepare("DELETE FROM SPMM.PROGRAMA");
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'message' => 'Todos os programas foram deletados: ' . implode(',', $programas)    
            ]);
        }
    });  
});