<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        
        $message_to_encrypt = $row['msg'] ;
        $secret_key = "my-secret-key";
        $method = "aes128";
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted_message = openssl_encrypt($message_to_encrypt, $method, $secret_key, 0, $iv);

        $decrypted_message = openssl_decrypt($encrypted_message, $method, $secret_key, 0, $iv);

        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'.$row['msg']  .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p>'.$row['msg'].'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">Sem mensagens disponiveis.Envie uma agora :)</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }

?>

