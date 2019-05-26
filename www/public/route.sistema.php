<?php

$app->group('/sistemas', function() {
    $this->get('/sistema', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.SISTEMA ORDER BY TX_SISTEMA");
        $sth->execute();
        return $res->withStatus(200)->withJson($sth->fetchAll());
    });

    $this->post('/sistema', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("INSERT INTO SPMM.SISTEMA (TX_SISTEMA) VALUES (:TX_SISTEMA)");
        $sth->bindParam(':TX_SISTEMA', $TX_SISTEMA);
        if ($sth->execute()) {
            $sth = $this->pdo->prepare("SELECT LAST_INSERT_ID() AS ID");
            $sth->execute();

            return $res->withStatus(200)->withJson([
                'ID_SISTEMA' => $sth->fetch()['id']
            ]);
        }
    });

    $this->put('/sistema/{id:[0-9]+}', function($req, $res, $params) {
        extract($req->getParsedBody());
        $sth = $this->pdo->prepare("UPDATE SPMM.SISTEMA SET TX_SISTEMA=:TX_SISTEMA WHERE ID_SISTEMA = :ID_SISTEMA");
        $sth->bindParam(':TX_SISTEMA', $TX_SISTEMA);
        $sth->bindParam(':ID_SISTEMA', $params['id']);
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_SISTEMA' => $params['id'],
                'TX_SISTEMA' => $TX_SISTEMA
            ]); 
        }
    });

    $this->delete('/sistema/{id:[0-9]+}', function($req, $res, $params) {
        $sth = $this->pdo->prepare("DELETE FROM SPMM.SISTEMA WHERE ID_SISTEMA = :ID_SISTEMA");
        $sth->bindParam(':ID_SISTEMA', $params['id']);

        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'ID_SISTEMA' => $params['id']
            ]);
        }
    });

    $this->delete('/sistema/all', function($req, $res, $params) {
        $sth = $this->pdo->prepare("SELECT * FROM SPMM.SISTEMA ORDER BY TX_SISTEMA");
        $sistemas = $sth->fetchAll();

        $sth = $this->pdo->prepare("DELETE FROM SPMM.SISTEMA");
        if ($sth->execute()) {
            return $res->withStatus(200)->withJson([
                'message' => 'Todos os sistemas foram deletados: ' . implode(',', $sistemas)    
            ]);
        }
    });

    $this->get('/sistema/acesso', function($req, $res, $params) {
        extract($req->getParams());

        $sth = $this->pdo->prepare("SELECT 
                                    G.TX_GRUPO,
                                    U.TX_USUARIO,
                                    S.TX_SISTEMA,
                                    M.TX_MODULO,
                                    P.TX_PROGRAMA,
                                    CA.CS_NIVEL_ACESSO 
                                    
                                    FROM CONTROLE_ACESSO CA 
                                    
                                    INNER JOIN SISTEMA S ON CA.ID_SISTEMA = S.ID_SISTEMA
                                    INNER JOIN MODULO M ON CA.ID_MODULO = M.ID_MODULO
                                    INNER JOIN PROGRAMA P ON CA.ID_PROGRAMA = P.ID_PROGRAMA
                                    INNER JOIN USUARIO U
                                    INNER JOIN GRUPO G
                                    INNER JOIN GRUPO_USUARIO GU 
                                    ON  GU.ID_USUARIO = U.ID_USUARIO
                                    AND GU.ID_GRUPO = G.ID_GRUPO
                                    AND CA.ID_GRUPO_USUARIO = GU.ID_GRUPO_USUARIO
                                    
                                    WHERE S.ID_SISTEMA = :ID_SISTEMA 
                                    AND M.ID_MODULO = :ID_MODULO 
                                    AND P.ID_PROGRAMA = :ID_PROGRAMA
                                    AND U.ID_USUARIO = :ID_USUARIO");
                                    
        $sth->bindParam(':ID_SISTEMA', $ID_SISTEMA);
        $sth->bindParam(':ID_MODULO', $ID_MODULO);
        $sth->bindParam(':ID_PROGRAMA', $ID_PROGRAMA);
        $sth->bindParam(':ID_USUARIO', $ID_USUARIO);

        $sth->execute();

        return $res->withStatus(200)->withJson($sth->fetchAll());
    });
});