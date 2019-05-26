<?php

$app->group('/usuarios', function() {
    $this->get('/usuario', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.USUARIO ORDER BY TX_USUARIO");
        $sth->execute();
        return $res->withStatus(200)->withJson($sth->fetchAll());
    });

    $this->post('/usuario', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("INSERT INTO SPMM.USUARIO (TX_USUARIO) VALUES (:TX_USUARIO)");
        $sth->bindParam(':TX_USUARIO', $TX_USUARIO);
        if ($sth->execute()) {
            $sth = $this->pdo->prepare("SELECT LAST_INSERT_ID() AS ID");
            $sth->execute();

            return $res->withStatus(200)->withJson([
                'ID_USUARIO' => $sth->fetch()['id']
            ]);
        }
    });

    $this->put('/usuario/{id:[0-9]+}', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("UPDATE SPMM.USUARIO SET TX_USUARIO=:TX_USUARIO WHERE ID_USUARIO = :ID_USUARIO");
        $sth->bindParam(':TX_USUARIO', $TX_USUARIO);
        $sth->bindParam(':ID_USUARIO', $params['id']);
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_USUARIO' => $params['id'],
                'TX_USUARIO' => $TX_USUARIO
            ]); 
        }
    });

    $this->delete('/usuario/{id:[0-9]+}', function($req, $res, $params) {
        $sth = $this->pdo->prepare("DELETE FROM SPMM.USUARIO WHERE ID_USUARIO = :ID_USUARIO");
        $sth->bindParam(':ID_USUARIO', $params['id']);

        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_USUARIO' => $params['id']
            ]);
        }
    });

    $this->delete('/usuario/all', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.USUARIO ORDER BY TX_USUARIO");
        $usuarios = $sth->fetchAll();

        $sth = $this->pdo->prepare("DELETE FROM SPMM.USUARIO");
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'message' => 'Todos os usuarios foram deletados: ' . implode(',', $usuarios)    
            ]);
        }
    });  
});