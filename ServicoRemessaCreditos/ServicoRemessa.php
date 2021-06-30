/<?php 

$handle = fopen("../PagamentosZeEstadioAmigo.csv", "r");
$header = fgetcsv($handle, 1000, ",");
?> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<table class="table table-bordered col-md-12">

    <thead>
        <th> CPF </th>
        <th>Mensagem</th>
        <th> Valor R$ </th>
        <th> Id da Transferência </th> 
        <th> Status da transferência </th> 
        <th> Data da transferência  </th>
        <th> Tipo da operação </th> 
    </thead>
    <tbody>
<?php

$token = getTokenAuth(); 
while ($row = fgetcsv($handle, 1000, ",")) {

    if(isset($row[1])){
    $cpf = str_replace('.','',$row[0]); 
    $cpf = str_replace('-','',$cpf);
    $valor = str_replace(';','',$row[1]);   

    if(is_numeric($valor)){ 
    
    
    $returnTransfer = SendTransfer($cpf, $valor, $token); 

    if(isset($returnTransfer['message']) != 1){
    ?>
    <tr>
    <td><?php echo $cpf; ?></td>
    <td><?php echo $returnTransfer['status']; ?></td>
    <td><?php echo $returnTransfer['value']; ?></td>
    <td><?php echo $returnTransfer['transfer_id']; ?></td>
    <td><?php echo $returnTransfer['status'] ?></td>
    <td><?php echo date('d-m-Y H:i:s',strtotime($returnTransfer['transfered_at'])); ?></td>
    <td><?php echo $returnTransfer['operacao']; ?></td>
    </tr>

<?php
    }else { ?>
    
    <tr>
    <td><?php echo $cpf; ?></td>
    <td><?php echo $returnTransfer['message']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>

    </tr>
    
    <?php 
    }
    }
}
}
?> 
</tbody>
</table>

<?php

fclose($handle);

function getTokenAuth(){ 
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.picpay.com/oauth2/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=01f7154e-2730-4d47-827b-aa353353c185&client_secret=92f29b80-1565-44f3-b4f1-65d11d4a8c0e&scope=openid',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $data = json_decode($response);

    return $data->access_token; 

}

function SendTransfer($cpf,$valor,$token){ 

    $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.picpay.com/v1/b2p/transfer',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'consumer="'.$cpf.'"&value='.$valor.'&not_withdrawable=0',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer '.$token,
        ),
        ));

    $response = curl_exec($curl);


    curl_close($curl);

    $data = json_decode($response);
    return (array)$data; 
}
?> 

