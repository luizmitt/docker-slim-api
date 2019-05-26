<?php

$app->group('/auth', function() {
    $this->post('/login', function($req, $res, $params) {
        extract($req->getParsedBody());

        $sth = $this->pdo->prepare("SELECT 
                                    U.TX_USUARIO, 
                                    P.TX_EMAIL, 
                                    PF.TX_CPF, 
                                    PJ.TX_CNPJ, 
                                    PF.TX_RG, 
                                    U.TX_SENHA 
                                    FROM PESSOA P
                                    INNER JOIN USUARIO U ON P.ID_PESSOA = U.ID_PESSOA
                                    LEFT JOIN PESSOA_FISICA PF ON P.ID_PESSOA = PF.ID_PESSOA
                                    LEFT JOIN PESSOA_JURIDICA PJ ON P.ID_PESSOA = PJ.ID_PESSOA
                                    WHERE ((U.TX_USUARIO = ?) OR (PF.TX_CPF = ?) OR (P.TX_EMAIL = ?) OR (PJ.TX_CNPJ = ?) OR (PF.TX_RG = ?) ) 
                                    AND (U.TX_SENHA = ?)");

        $sth->execute([$username, $username, $username, $username, $username, $password]);

        return $res->withStatus(200)->withJson($sth->fetch());
    });
});