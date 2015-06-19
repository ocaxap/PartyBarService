<?php
session_start();
$admin = 'ocaxap@gmail.com';

if ( isset( $_POST['sendMail'] ) ) {
  $name  = substr( $_POST['name'], 0, 64 );
  $email   = substr( $_POST['email'], 0, 64 );
  $subject = substr( $_POST['subject'], 0, 64 );
  $message = substr( $_POST['message'], 0, 250 );
  
  $error = '';
  if ( empty( $name ) ) $error = $error.'<li>Не заполнено поле "Имя"</li>';
  if ( empty( $email ) ) $error = $error.'<li>Не заполнено поле "E-mail"</li>';
  if ( empty( $subject ) ) $error = $error.'<li>Не заполнено поле "Тема"</li>';
  if ( empty( $message ) ) $error = $error.'<li>Не заполнено поле "Сообщение"</li>';
  if ( !empty( $email ) and !preg_match( "#^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,6}$#i", $email ) )
    $error = $error.'<li>поле "E-mail" должно соответствовать формату somebody@somewhere.ru</li>';
  if ( !empty( $error ) ) {
    $_SESSION['sendMailForm']['error']   = '<p>При заполнении формы были допущены ошибки:</p><ul>'.$error.'</ul>';
    $_SESSION['sendMailForm']['name']    = $name;
    $_SESSION['sendMailForm']['email']   = $email;
    $_SESSION['sendMailForm']['subject'] = $subject;
    $_SESSION['sendMailForm']['message'] = $message;
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  
  $body = "АВТОР:\r\n".$name."\r\n\r\n";
  $body .= "E-MAIL:\r\n".$email."\r\n\r\n";
  $body .= "ТЕМА:\r\n".$subject."\r\n\r\n";
  $body .= "СООБЩЕНИЕ:\r\n".$message;
  $body = quoted_printable_encode( $body );

  $theme   = '=?windows-1251?B?'.base64_encode('Заполнена форма на сайте').'?=';
  $headers = "From: ".$_SERVER['SERVER_NAME']." <".$email.">\r\n";
  $headers = $headers."Return-path: <".$email.">\r\n";
  $headers = $headers."Content-type: text/plain; charset=\"windows-1251\"\r\n";
  $headers = $headers."Content-Transfer-Encoding: quoted-printable\r\n\r\n";
  
  if ( mail($admin, $theme, $body, $headers) )
    $_SESSION['success'] = true;
  else
    $_SESSION['success'] = false;
  header( 'Location: '.$_SERVER['PHP_SELF'] );
  die();
}
 
function quoted_printable_encode ( $string ) {
   // rule #2, #3 (leaves space and tab characters in tact)
   $string = preg_replace_callback (
   '/[^\x21-\x3C\x3E-\x7E\x09\x20]/',
   'quoted_printable_encode_character',
   $string
   );
   $newline = "=\r\n"; // '=' + CRLF (rule #4)
   // make sure the splitting of lines does not interfere with escaped characters
   // (chunk_split fails here)
   $string = preg_replace ( '/(.{73}[^=]{0,3})/', '$1'.$newline, $string);
   return $string;
}

function quoted_printable_encode_character ( $matches ) {
   $character = $matches[0];
   return sprintf ( '=%02x', ord ( $character ) );
}
?>

<?php
if ( isset( $_SESSION['success'] ) ) {
  if ( $_SESSION['success'] )
    echo '<p>Письмо успешно отправлено</p>';
  else
    echo '<p>Ошибка при отправке письма</p>';
  unset( $_SESSION['success'] );
}
if ( isset( $_SESSION['sendMailForm'] ) ) {
  echo $_SESSION['sendMailForm']['error'];
  $name    = htmlspecialchars ( $_SESSION['sendMailForm']['name'] );
  $email   = htmlspecialchars ( $_SESSION['sendMailForm']['email'] );
  $subject = htmlspecialchars ( $_SESSION['sendMailForm']['subject'] );
  $message = htmlspecialchars ( $_SESSION['sendMailForm']['message'] );
  unset( $_SESSION['sendMailForm'] );
} else {
  $name    = '';
  $email   = '';
  $subject = '';
  $message = '';
}
?>