<?php
############################################################################################
# @SuporteMasterBot Dev by: Nilsonlinux                                                    #
############################################################################################
  define('BOT_TOKEN', '1511931883:AAHVHAhOF6aFZk-cv7MeBT5jQ-GOwVh8DNc');
  define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
  ///////////////////////////////////////////////////////////////
  function apiRequestWebhook($method, $parameters) {
    if (!is_string($method)) {
      error_log("Method name must be a string\n");
      return false;
    }
    if (!$parameters) {
      $parameters = array();
    } else if (!is_array($parameters)) {
      error_log("Parameters must be an array\n");
      return false;
    }
    $parameters["method"] = $method;
    header("Content-Type: application/json");
    echo json_encode($parameters);
    return true;
  }
  function exec_curl_request($handle) {
    $response = curl_exec($handle);
    if ($response === false) {
      $errno = curl_errno($handle);
      $error = curl_error($handle);
      error_log("Curl returned error $errno: $error\n");
      curl_close($handle);
      return false;
    }
    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);
    if ($http_code >= 500) {
      // do not wat to DDOS server if something goes wrong
      sleep(10);
      return false;
    } else if ($http_code != 1) {
      $response = json_decode($response, true);
      error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
      if ($http_code == 1) {
        throw new Exception('Invalid access token provided');
      }
      return false;
    } else {
      $response = json_decode($response, true);
      if (isset($response['description'])) {
        error_log("Request was successful: {$response['description']}\n");
      }
      $response = $response['result'];
    }
    return $response;
  }
  function apiRequest($method, $parameters) {
    if (!is_string($method)) {
      error_log("Method name must be a string\n");
      return false;
    }
    if (!$parameters) {
      $parameters = array();
    } else if (!is_array($parameters)) {
      error_log("Parameters must be an array\n");
      return false;
    }
    foreach ($parameters as $key => &$val) {
      // encoding to JSON array parameters, for example reply_markup
      if (!is_numeric($val) && !is_string($val)) {
        $val = json_encode($val);
      }
    }
    $url = API_URL.$method.'?'.http_build_query($parameters);
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    return exec_curl_request($handle);
  }
  function apiRequestJson($method, $parameters) {
    if (!is_string($method)) {
      error_log("Method name must be a string\n");
      return false;
    }
    if (!$parameters) {
      $parameters = array();
    } else if (!is_array($parameters)) {
      error_log("Parameters must be an array\n");
      return false;
    }
    $parameters["method"] = $method;
    $handle = curl_init(API_URL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    return exec_curl_request($handle);
  }
  function processMessage($message) {
    // process incoming message
    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];
    if (isset($message['text'])) {
      // incoming text message
      $text = $message['text'];
      //START - INICIO
      if (strpos($text, "/start") === 0) {
        apiRequestJson("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`VOCÊ ESTÁ NO MENU PRINCIPAL.
SELECIONE O GRUPO DE SUA FILIAL NO MENU ABAIXO.`
👋Olá, '.$message['from']['first_name'].' .Se procura outras informações sobre sua loja ou de outra filial, favor falar com @SuporteMasterBot', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//START - FIM
              } else if ($text === "🌘 Fechamento") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "HTML", "text" => '
<code>VOCÊ ESTÁ NO MENU FECHAMENTO DE LOJAS.
SELECIONE O GRUPO DE SUA FILIAL NO MENU ABAIXO.</code>
', 'reply_markup' => array(
  'keyboard' => array(array('🌘 MATEUS LOJAS', 'CAMIÑO LOJAS 🌘')),
  'resize_keyboard' => true)));
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
} else if ($text === "CAMIÑO LOJAS 🌘") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*VOCÊ SELECIONOU O GRUPO* `POSTERUS SUPERMERCADOS LTDA`
*AGORA SELECIONE SUA* `FILIAL` *PARA INICIAR
O PROCESSO* `FECHAMENTO DE LOJA.`
', 'reply_markup' => array(
  'keyboard' => array(array('🌘 LOJA 431', '🌘 LOJA 433', '🌘 LOJA 434'),array('🌘 LOJA 435', '🌘 LOJA 436', '🌘 LOJA 439'),array('🌘 LOJA 445', '🌘 LOJA 446', '🌘 LOJA 447'),array('🌘 LOJA 450', '🌘 LOJA 451'),array('☀️ Abertura', '🌘 Fechamento'),array('🌘 MATEUS LOJAS')),
  'resize_keyboard' => true)));
///LOJA SELECIONAR FECHAMENTO
} else if ($text === "🌘 MATEUS LOJAS") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*VOCÊ SELECIONOU O GRUPO* `MATEUS SUPERMERCADOS S.A`
*AGORA SELECIONE SUA* `FILIAL` *PARA INICIAR
O PROCESSO* `FECHAMENTO DE LOJA.`
', 'reply_markup' => array(
  'keyboard' => array(array('🌘 Loja 03', '🌘 Loja 32', '🌘 Loja 39', '🌘 Loja 40'),array('🌘 Loja 41', '🌘 Loja 42', '🌘 Loja 47', '🌘 Loja 48'),array('🌘 Loja 91', '🌘 Loja 97', '🌘 Loja 99', '🌘 Loja 202'),array('🌘 Loja 207', '🌘 Loja 251', '🌘 Loja 252'),array('☀️ Abertura', '🌘 Fechamento'),array('CAMIÑO LOJAS 🌘')),
  'resize_keyboard' => true)));
//LOJA SELECIONAR FECHAMENTO FIM
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
              } else if ($text === "☀️ Abertura") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "HTML", "text" => '
<code>VOCÊ ESTÁ NO MENU ABERTURA DE LOJAS.
SELECIONE O GRUPO DE SUA FILIAL NO MENU ABAIXO.</code>
', 'reply_markup' => array(
  'keyboard' => array(array('☀️ MATEUS LOJAS', 'CAMIÑO LOJAS ☀️')),
  'resize_keyboard' => true)));
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
} else if ($text === "CAMIÑO LOJAS ☀️") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*VOCÊ SELECIONOU O GRUPO* `POSTERUS SUPERMERCADOS LTDA`
*AGORA SELECIONE SUA* `FILIAL` *PARA INICIAR
O PROCESSO* `ABERTURA DE LOJA.`
', 'reply_markup' => array(
  'keyboard' => array(array('☀️ LOJA 431', '☀️ LOJA 433', '☀️ LOJA 434'),array('☀️ LOJA 435', '☀️ LOJA 436', '☀️ LOJA 439'),array('☀️ LOJA 445', '☀️ LOJA 446', '☀️ LOJA 447'),array('☀️ LOJA 450', '☀️ LOJA 451'),array('☀️ Abertura', '🌘 Fechamento'),array('☀️ MATEUS LOJAS')),
  'resize_keyboard' => true)));
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
              } else if ($text === "CAMIÑO LOJAS ☀️") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*VOCÊ SELECIONOU O GRUPO* `POSTERUS SUPERMERCADOS LTDA`
*AGORA SELECIONE SUA* `FILIAL` *PARA INICIAR
O PROCESSO* `ABERTURA DE LOJA.`
', 'reply_markup' => array(
  'keyboard' => array(array('☀️ LOJA 431', '☀️ LOJA 433', '☀️ LOJA 434'),array('☀️ LOJA 435', '☀️ LOJA 436', '☀️ LOJA 439'),array('☀️ LOJA 445', '☀️ LOJA 446', '☀️ LOJA 447'),array('☀️ LOJA 450', '☀️ LOJA 451'),array('☀️ Abertura', '🌘 Fechamento'),array('☀️ MATEUS LOJAS')),
  'resize_keyboard' => true)));
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
              } else if ($text === "☀️ MATEUS LOJAS") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*VOCÊ SELECIONOU O GRUPO* `MATEUS SUPERMERCADOS S.A`
*AGORA SELECIONE SUA* `FILIAL` *PARA INICIAR
O PROCESSO* `ABERTURA DE LOJA.`
', 'reply_markup' => array(
  'keyboard' => array(array('☀️ Loja 03', '☀️ Loja 32', '☀️ Loja 39', '☀️ Loja 40'),array('☀️ Loja 41', '☀️ Loja 42', '☀️ Loja 47', '☀️ Loja 48'),array('☀️ Loja 91', '☀️ Loja 97', '☀️ Loja 99', '☀️ Loja 202'),array('☀️ Loja 207', '☀️ Loja 251', '☀️ Loja 252'),array('☀️ Abertura', '🌘 Fechamento'),array('CAMIÑO LOJAS ☀️')),
  'resize_keyboard' => true)));
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
      } else if ($text === "☀️ Loja 03") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 03 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 03')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 03") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 03.* `SUPER SANTA INÊS`
◾️*Santa Inês - MA*
◾*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 03 FINAL
//LOJA 03 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 03") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 03 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-03 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-03 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-03 ❌', 'CONSISTÊNCIA OK SM-03 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-03 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 03.* `SUPER SANTA INÊS`
◾️*Santa Inês - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-03 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 03.* `SUPER SANTA INÊS`
◾️*Santa Inês - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 03 FINAL FECHAMENTO
//LOJA 32 INICIO
      } else if ($text === "☀️ Loja 32") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 32 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 32')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 32") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 32.* `MIX TIMON`
◾️*Timon - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 32 FINAL.
//LOJA 32 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 32") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 32 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-32 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-32 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-32 ❌', 'CONSISTÊNCIA OK SM-32 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-32 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 32.* `MIX TIMON`
◾️*Timon - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-32 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 32.* `MIX TIMON`
◾️*Timon - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 32 FINAL FECHAMENTO
//LOJA 39 INICIO.
//LOJA 40 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 40") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 40 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-40 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-40 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-40 ❌', 'CONSISTÊNCIA OK SM-40 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-40 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 40.* `MATEUS BARRA DO CORDA`
◾️*Barra do Corda - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-40 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 40.*
◾️*Barra do Corda - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 39 FINAL FECHAMENTO
      } else if ($text === "☀️ Loja 39") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 39 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
        ', 'reply_markup' => array(
        'keyboard' => array(array('CONCLUIR ABERTURA LOJA 39')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
//LOJA 40 INICIO ABERTURA
      } else if ($text === "☀️ Loja 40") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 40 SELECIONADA* ✅
*Barra do Corda - MA*
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 40')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 40") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 40.*
◾️*Barra do Corda - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 40 FINAL
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 39") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 39.* `MIX CHAPADINHA`
◾️*Chapadinha - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 39 FINAL
//LOJA 39 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 39") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 39 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-39 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-39 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-39 ❌', 'CONSISTÊNCIA OK SM-39 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-39 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 39.* `MIX CHAPADINHA`
◾️*Chapadinha - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-39 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 39.* `MIX CHAPADINHA`
◾️*Chapadinha - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 39 FINAL FECHAMENTO
//LOJA 41 INICIO
      } else if ($text === "☀️ Loja 41") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 41 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
        ', 'reply_markup' => array(
        'keyboard' => array(array('CONCLUIR ABERTURA LOJA 41')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 41") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 41.* `MIX CAXIAS`
◾️*Caxias - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 41 FINAL
//LOJA 41 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 41") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 41 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-41 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-41 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-41 ❌', 'CONSISTÊNCIA OK SM-41 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-41 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 41.* `MIX CAXIAS`
◾️*Caxias - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-41 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 41.* `MIX CAXIAS`
◾️*Caxias - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 41 FINAL FECHAMENTO
//LOJA 42 INICIO
      } else if ($text === "☀️ Loja 42") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 42 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
        ', 'reply_markup' => array(
        'keyboard' => array(array('CONCLUIR ABERTURA LOJA 42')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 42") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 42.* `SUPER PRES. DUTRA`
◾️*Presidente Dutra - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 42 FINAL
//LOJA 42 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 42") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 42 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-42 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-42 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-42 ❌', 'CONSISTÊNCIA OK SM-42 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-42 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 42.* `SUPER PRES. DUTRA`
◾️*Presidente Dutra - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-42 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 42.* `SUPER PRES. DUTRA`
◾️*Presidente Dutra - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 42 FINAL FECHAMENTO
//LOJA 47 INICIO
      } else if ($text === "☀️ Loja 47") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 47 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
        ', 'reply_markup' => array(
        'keyboard' => array(array('CONCLUIR ABERTURA LOJA 47')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 47") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 47.* `MIX BACABAL`
◾️*Bacabal - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 47 FINAL
//LOJA 47 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 47") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 47 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-47 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-47 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-47 ❌', 'CONSISTÊNCIA OK SM-47 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-47 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 47.* `MIX BACABAL`
◾️*Bacabal - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-47 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 47.* `MIX BACABAL`
◾️*Bacabal - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 47 FINAL FECHAMENTO
//LOJA 48 INICIO
      } else if ($text === "☀️ Loja 48") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 48 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
        ', 'reply_markup' => array(
        'keyboard' => array(array('CONCLUIR ABERTURA LOJA 48')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 48") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 48.* `MIX PEDREIRAS`
◾️*Pedreiras - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 48 FINAL
//LOJA 48 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 48") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 48 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-48 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-48 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-48 ❌', 'CONSISTÊNCIA OK SM-48 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-48 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 48.* `MIX PEDREIRAS`
◾️*Pedreiras - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-48 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 48.* `MIX PEDREIRAS`
◾️*Pedreiras - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 48 FINAL FECHAMENTO
//LOJA 97 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 97") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 97 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-97 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-97 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-97 ❌', 'CONSISTÊNCIA OK SM-97 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-97 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 97.* `MIX TERESINA CEASA`
◾️*Teresina - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-97 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA* ✅
◾️*LOJA 97.* `MIX TERESINA CEASA`
◾️*Teresina - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 97 FINAL FECHAMENTO
//LOJA 97 INICIO
      } else if ($text === "☀️ Loja 97") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 97 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 97')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 97") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 97.* `MIX TERESINA CEASA`
◾️*Teresina - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 97 FINAL.
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
      } else if ($text === "☀️ Loja 99") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 99 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 99')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 99") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 99.* `MIX PINHEIRO`
◾️*Pinheiro - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 99 FINAL
//LOJA 99 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 99") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 99 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-99 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-99 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-99 ❌', 'CONSISTÊNCIA OK SM-99 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-99 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 99.* `MIX PINHEIRO`
◾️*Pinheiro - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-99 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 99.* `MIX PINHEIRO`
◾️*Pinheiro - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 99 FINAL FECHAMENTO
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX 202
      } else if ($text === "☀️ Loja 202") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
Loja 202 - Super Codó - MA / Selecionada✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 202')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 202") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO COM SUCESSO.`
◾️*LOJA 202.* `SUPER CODÓ`
◾️*Codó - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 202 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 202") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 202 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-202 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-202 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-202 ❌', 'CONSISTÊNCIA OK SM-202 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-202 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 202.* `SUPER CODÓ`
◾️*Codó - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-202 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 202.* `SUPER CODÓ`
◾️*Codó - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 202 FINAL FECHAMENTO
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
      } else if ($text === "☀️ Loja 207") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 207 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR.`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 207')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR ABERTURA
      } else if ($text === "CONCLUIR ABERTURA LOJA 207") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 207.* `SUPER BURITICUPÚ`
◾️*Buriticupú - MA*
◾*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 207 FINAL
//LOJA 207 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 207") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 207 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-207 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-207 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-207 ❌', 'CONSISTÊNCIA OK SM-207 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-207 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 207.* `SUPER BURITICUPÚ`
◾️*Buriticupú - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-207 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 207.* `SUPER BURITICUPÚ`
◾️*Buriticupú - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 207 FINAL FECHAMENTO
//LOJA 91 INICIO
      } else if ($text === "☀️ Loja 91") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 91 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
          ', 'reply_markup' => array(
          'keyboard' => array(array('CONCLUIR ABERTURA LOJA 91')),

          'resize_keyboard' => true)));
      } else if ($text === "CONCLUIR ABERTURA LOJA 91") {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 91.* `MIX SANTA INÊS`
◾️*Santa Inês - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 91 FINAL
//ACTIONS E COMANDOS ABERTURA DE LOJA - NILSONLINUX
//LOJA 91 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 91") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 91 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-91 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-91 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-91 ❌', 'CONSISTÊNCIA OK SM-91 ✅')),
        'resize_keyboard' => true)));
} else if ($text === "ERRO CONSISTÊNCIA SM-91 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 91.* `MIX SANTA INÊS`
◾️*Santa Inês - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-91 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 91.* `MIX SANTA INÊS`
◾️*Santa Inês - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 91 FINAL FECHAMENTO
//LOJA 251 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 251") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 251 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-251 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-251 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-251 ❌', 'CONSISTÊNCIA OK SM-251 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-251 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 251.* `MIX PARNAÍBA`
◾️*Parnaíba - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 251 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-251 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 251.* `MIX PARNAÍBA`
◾️*Parnaíba - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 251 FINAL FECHAMENTO
//LOJA 251 INICIO ABERTURA
} else if ($text === "☀️ Loja 251") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 251 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 251')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 251") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
  ◾️*LOJA 251.* `MIX PARNAÍBA`
  ◾️*Parnaíba - PI*
  ◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
            'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
            'resize_keyboard' => true)));
//LOJA 251 FINAL ABERTURA
//LOJA 252 INICIO ABERTURA
} else if ($text === "☀️ Loja 252") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 252 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 252')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 252") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 252.* `MIX TERESINA`
◾️*Teresina - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 252 FINAL ABERTURA
//LOJA 252 INICIO FECHAMENTO
} else if ($text === "🌘 Loja 252") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 252 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-252 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-252 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-252 ❌', 'CONSISTÊNCIA OK SM-252 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-252 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 252.* `MIX TERESINA`
◾️*Teresina - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 251 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-252 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 252.* `MIX TERESINA`
◾️*Teresina - PI*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 252 FINAL FECHAMENTO



//LOJA 431 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 431") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 431 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 431')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 431") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 431.* `CAMIÑO LAGO DA PEDRA`
◾️*Lago da Pedra - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 431 FINAL ABERTURA
//LOJA 431 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 431") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 431 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-431 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-431 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-431 ❌', 'CONSISTÊNCIA OK SM-431 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-431 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 431.* `CAMIÑO LAGO DA PEDRA`
◾️*Lago da Pedra - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 431 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-431 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 431.* `CAMIÑO LAGO DA PEDRA`
◾️*Lago da Pedra - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 431 FINAL FECHAMENTO ########################################################################
//LOJA 433 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 433") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 433 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 433')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 433") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 433.* `CAMIÑO VIANA`
◾️*Viana - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 431 FINAL ABERTURA
//LOJA 431 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 433") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 433 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-433 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-433 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-433 ❌', 'CONSISTÊNCIA OK SM-433 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-433 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 433.* `CAMIÑO VIANA`
◾️*Viana - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 433 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-433 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 433.* `CAMIÑO VIANA`
◾️*Viana - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 433 FINAL FECHAMENTO ########################################################################
//LOJA 434 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 434") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 434 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 434')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 434") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 434.* `CAMIÑO BARREIRINHAS`
◾️*Barreirinhas - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 434 FINAL ABERTURA
//LOJA 434 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 434") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 434 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-434 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-434 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-434 ❌', 'CONSISTÊNCIA OK SM-434 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-434 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 434.* `CAMIÑO BARREIRINHAS`
◾️*Barreirinhas - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 434 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-434 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 434.* `CAMIÑO BARREIRINHAS`
◾️*Barreirinhas - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 434 FINAL FECHAMENTO ########################################################################
//LOJA 435 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 435") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 435 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 435')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 434") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 435.* `CAMIÑO COROATÁ`
◾️*Coroatá - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 435 FINAL ABERTURA
//LOJA 435 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 435") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 435 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-435 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-435 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-435 ❌', 'CONSISTÊNCIA OK SM-435 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-435 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 435.* `CAMIÑO COROATÁ`
◾️*Coroatá - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 435 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-435 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 435.* `CAMIÑO COROATÁ`
◾️*Coroatá - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 435 FINAL FECHAMENTO ########################################################################
//LOJA 445 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 445") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 445 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 445')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 445") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 445.* `CAMIÑO UTAPECURÚMIRIM`
◾️*Itapecurúmirim - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 445 FINAL ABERTURA
//LOJA 445 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 445") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 445 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-445 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-445 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-445 ❌', 'CONSISTÊNCIA OK SM-445 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-445 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 445.* `CAMIÑO UTAPECURÚMIRIM`
◾️*Itapecurúmirim - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 445 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-445 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 445.* `CAMIÑO UTAPECURÚMIRIM`
◾️*Itapecurúmirim - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 445 FINAL FECHAMENTO ########################################################################
//LOJA 446 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 446") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 446 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 446')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 446") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 446.* `CAMIÑO UTAPECURÚMIRIM`
◾️*Itapecurúmirim - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 446 FINAL ABERTURA
//LOJA 446 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 446") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 446 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-446 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-446 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-446 ❌', 'CONSISTÊNCIA OK SM-446 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-446 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 446.* `CAMIÑO UTAPECURÚMIRIM`
◾️*Itapecurúmirim - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 446 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-446 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 446.* `CAMIÑO SANTA LUZIA`
◾️*Santa luzia - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 446 FINAL FECHAMENTO ########################################################################
//LOJA 447 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 447") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 447 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 447')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 447") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 447.* `CAMIÑO TUTÓIA`
◾️*Tutóia - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 447 FINAL ABERTURA
//LOJA 447 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 447") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 447 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-447 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-447 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-447 ❌', 'CONSISTÊNCIA OK SM-447 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-447 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 447.* `CAMIÑO TUTÓIA`
◾️*Tutóia - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 447 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-447 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 447.* `CAMIÑO TUTÓIA`
◾️*Tutóia - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 447 FINAL FECHAMENTO ########################################################################
//LOJA 450 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 450") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 450 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 450')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 450") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 450.* `CAMIÑO ZÉ DOCA`
◾️*Zé Doca - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 450 FINAL ABERTURA
//LOJA 450 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 450") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 450 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-450 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-450 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-450 ❌', 'CONSISTÊNCIA OK SM-450 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-450 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 450.* `CAMIÑO ZÉ DOCA`
◾️*Zé Doca - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 450 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-450 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 450.* `CAMIÑO ZÉ DOCA`
◾️*Zé Doca - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 450 FINAL FECHAMENTO ########################################################################
//LOJA 451 FINAL FECHAMENTO ########################################################################
} else if ($text === "☀️ LOJA 451") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 451 SELECIONADA* ✅
`ENVIE O PAINEL TERMINAL ATUALIZADO DE SUA LOJA`
`E EM SEGUIDA CLIQUE EM CONCLUIR`
    ', 'reply_markup' => array(
    'keyboard' => array(array('CONCLUIR ABERTURA LOJA 451')),
      'resize_keyboard' => true)));
} else if ($text === "CONCLUIR ABERTURA LOJA 451") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO ABERTURA FINALIZADO.`
◾️*LOJA 451.* `CAMIÑO VARGEM GRANDE`
◾️*Vargem Grande - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 451 FINAL ABERTURA
//LOJA 451 INICIO FECHAMENTO
} else if ($text === "🌘 LOJA 451") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
*LOJA 451 SELECIONADA* ✅
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
*FAÇA O ANEXO DOS SEGUINTES PRINTS*
`PAINEL TERMINAL`, `ALTERAÇÃO GMCORE`
`CARGA DAS BALANÇAS E CONSISTÊNCIA`
*EM CASOS DE HAVER ALGUM PDV COM ERRO*
*NA CONSISTÊNCIA CLIQUE EM*
*ERRO CONSISTÊNCIA SM-451 ❌*
*OU SE TODOS OBTERAM SUCESSO CLIQUE EM*
*CONSISTÊNCIA OK SM-451 ✅*
⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️
        ', 'reply_markup' => array(
        'keyboard' => array(array('ERRO CONSISTÊNCIA SM-451 ❌', 'CONSISTÊNCIA OK SM-451 ✅')),
        'resize_keyboard' => true)));
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "ERRO CONSISTÊNCIA SM-451 ❌") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO.`
◾️*ERRO NA CONSISTÊNCIA* ❌
◾️*LOJA 451.* `CAMIÑO VARGEM GRANDE`
◾️*Vargem Grande - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 451 FINAL FECHAMENTO
//BOTÃO CONCLUIR FECHAMENTO
} else if ($text === "CONSISTÊNCIA OK SM-451 ✅") {
  apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => '
`PROCESSO FECHAMENTO FINALIZADO COM SUCESSO.`
◾️*CONSISTÊNCIA OK* ✅
◾️*LOJA 451.* `CAMIÑO VARGEM GRANDE`
◾️*Vargem Grande - MA*
◾️*CPD* - '. $message['from']['first_name'].' ', 'reply_markup' => array(
          'keyboard' => array(array('☀️ Abertura', '🌘 Fechamento')),
          'resize_keyboard' => true)));
//LOJA 451 FINAL FECHAMENTO ########################################################################

//Em desenvolvimento. - Nilsonlinux
      } else if (strpos($text, "/stop") === 0) {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "parse_mode" => "Markdown", "text" => 'Você saiu🕴.', 'reply_markup' => array(
          'keyboard' => array(array('🏠')),

          'resize_keyboard' => true)));
      }
    }
  }
  define('WEBHOOK_URL', '###');
  if (php_sapi_name() == 'cli') {
    // if run from console, set or delete webhook
    apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
    exit;
  }
  $content = file_get_contents("php://input");
  $update = json_decode($content, true);
  if (!$update) {
    // receive wrong update, must not happen
    exit;
  }
  if (isset($update["message"])) {
    processMessage($update["message"]);
  }
