<?php

$app->group('/grupos', function() {
    $this->get('/grupo', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.GRUPO ORDER BY TX_GRUPO");
        $sth->execute();
        return $res->withStatus(200)->withJson($sth->fetchAll());
    });

    $this->post('/grupo', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("INSERT INTO SPMM.GRUPO (TX_GRUPO) VALUES (:TX_GRUPO)");
        $sth->bindParam(':TX_GRUPO', $TX_GRUPO);
        if ($sth->execute()) {
            $sth = $this->pdo->prepare("SELECT LAST_INSERT_ID() AS ID");
            $sth->execute();

            return $res->withStatus(200)->withJson([
                'ID_GRUPO' => $sth->fetch()['id']
            ]);
        }
    });

    $this->put('/grupo/{id:[0-9]+}', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("UPDATE SPMM.GRUPO SET TX_GRUPO=:TX_GRUPO WHERE ID_GRUPO = :ID_GRUPO");
        $sth->bindParam(':TX_GRUPO', $TX_GRUPO);
        $sth->bindParam(':ID_GRUPO', $params['id']);
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_GRUPO' => $params['id'],
                'TX_GRUPO' => $TX_GRUPO
            ]); 
        }
    });

    $this->delete('/grupo/{id:[0-9]+}', function($req, $res, $params) {
        $sth = $this->pdo->prepare("DELETE FROM SPMM.GRUPO WHERE ID_GRUPO = :ID_GRUPO");
        $sth->bindParam(':ID_GRUPO', $params['id']);

        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_GRUPO' => $params['id']
            ]);
        }
    });

    $this->delete('/grupo/all', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.GRUPO ORDER BY TX_GRUPO");
        $grupos = $sth->fetchAll();

        $sth = $this->pdo->prepare("DELETE FROM SPMM.GRUPO");
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'message' => 'Todos os grupos foram deletados: ' . implode(',', $grupos)    
            ]);
        }
    });  

    $this->get('/grupo/usuario', function($req, $res, $params) {

        $where = null;

        foreach ($req->getParams() as $key => $value) {
            $where[] = "{$key} = :{$key}";
        }

        if (!empty($where)) {
            $where = ' WHERE ' . implode(' AND ', $where);
        } 

        $sth = $this->pdo->prepare("SELECT * from GRUPO_USUARIO GU
        inner JOIN GRUPO G ON GU.ID_GRUPO = G.ID_GRUPO
        Inner JOIN USUARIO U ON GU.ID_USUARIO = U.ID_USUARIO
        $where
        ");

        if (!empty($where)) {
            foreach ($req->getParams() as $key => $value) {
                $sth->bindParam(':'.$key, $value);
            }
        }

        $sth->execute();

        $data = $sth->fetchAll();

        foreach ($data as $index => $array) {
            if (isset($array['tx_senha'])) {
                unset($data[$index]['tx_senha']);
            }
        }

        return $res->withStatus(200)->withJson($data);
    });
});