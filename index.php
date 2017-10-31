<!DOCTYPE html>

<html>
  <head>
    <meta charset="utf-8">
    <title>Brute-Force Attack</title>
    <link rel="stylesheet" href="./css/index.css">
  </head>
  <body>
    <div name="background_opacity">

      <div name="login_div">
        <h1 name="login_title">LOGIN</h1>
        <form name="login_form" action="./index.php" method="post">
          <input type="text" name="Id_Input" placeholder="Id">
          <input type="password" name="Pass_Input" placeholder="Pass">
          <input type="submit" name="sub_Input" value="Login">
        </form>
        <button type="button" name="format_Btn">Forgot E-mail/Password</button>
      </div>

      <div id="DB_Process">
        <h1 name="Process_Title">DB Process</h1>
        <div id="DB_Process_Monitor">

        </div>
      </div>

    </div>
  </body>
</html>
<?php
  function Login($ID,$PW){
    include_once('./Config/Pick_Config.php');
      $SQL = "SELECT COUNT(*),WRONG_COUNT,NAME FROM Brute_User WHERE ID='".base64_encode($ID)."' AND PW='".base64_encode($PW)."';";
      $Result = mysqli_query($Conn,$SQL);
      if(($Row = mysqli_fetch_row($Result))){
        if($Row[0]>0 && $Row[1]<5){ //아이디, 비밀번호와 일치하는 계정이 존재하고 계정이 잠기지 않았을때
          $User_Name = base64_decode($Row[2]);
          $SQL_WR_UPDATE = "UPDATE Brute_User SET WRONG_COUNT=0 WHERE ID='".base64_encode($ID)."';";
          mysqli_query($Conn,$SQL_WR_UPDATE);
          echo
           "<script>
              var DB_Process_Div = document.getElementById('DB_Process_Monitor');
              DB_Process_Div.innerHTML += 'Result_Message : SUCCESS<br>';
              DB_Process_Div.innerHTML += 'Account ID : '+'".$ID."<br>';
              DB_Process_Div.innerHTML += '계정 존재여부 : TRUE<br>';
              DB_Process_Div.innerHTML += '틀린 횟수 : ".$Row[1]."<br>';
              DB_Process_Div.innerHTML += '틀린 횟수 초기화 : 완료';
            </script>
          ";

          return "반갑습니다. ".$User_Name."님";
        }

        else { //아이디, 비밀번호와 일치하는 계정이 없을때
          $SQL_EXIST_ID = "SELECT COUNT(*),WRONG_COUNT FROM Brute_User WHERE ID='".base64_encode($ID)."';";
          $Result_EXIST_ID = mysqli_query($Conn,$SQL_EXIST_ID);
          if(($Row_EXIST_ID = mysqli_fetch_row($Result_EXIST_ID))){
            if($Row_EXIST_ID[0] > 0 && $Row_EXIST_ID[1] < 5){ //아이디는 일치하나, 비밀번호가 틀렸을때
              $WRONGCOUNT = $Row_EXIST_ID[1]+1;
              $SQL_WR_UPDATE = "UPDATE Brute_User SET WRONG_COUNT=".$WRONGCOUNT." WHERE ID='".base64_encode($ID)."';";
              mysqli_query($Conn,$SQL_WR_UPDATE);
              $WRONG_Remaining_Number = 5 - $WRONGCOUNT;
              if($WRONGCOUNT < 5){ //틀린횟수가 5회미만 : 계정 잠금 카운트 증가
                echo
                 "<script>
                    var DB_Process_Div = document.getElementById('DB_Process_Monitor');
                    DB_Process_Div.innerHTML += 'Result_Message : SUCCESS<br>';
                    DB_Process_Div.innerHTML += 'Account ID : '+'".$ID."<br>';
                    DB_Process_Div.innerHTML += '계정 존재여부 : FALSE<br>';
                    DB_Process_Div.innerHTML += '틀린 횟수 : ".$WRONG_COUNT."<br>';
                  </script>
                ";
                return "아이디 또는 비밀번호가 틀렸습니다. (계정 잠금까지 남은 횟수 : ".$WRONG_Remaining_Number.")";
              }else { //틀린횟수가 5회이상 : 계정 잠금상태 변경
                echo
                 "<script>
                    var DB_Process_Div = document.getElementById('DB_Process_Monitor');
                    DB_Process_Div.innerHTML += 'Result_Message : CHANGE ACCOUNT LOCK<br>';
                    DB_Process_Div.innerHTML += 'Account ID : '+'".$ID."<br>';
                    DB_Process_Div.innerHTML += '틀린 횟수 : 5<br>';
                  </script>
                ";
                return "해당 계정이 잠금상태로 변경되었습니다.";
              }
            }
            else if($Row_EXIST_ID[0]>0 && $Row_EXIST_ID[1]>=5){ //아이디,비밀번호와 일치하는 계정은 존재하나 계정이 잠겨있을 때
              echo
               "<script>
                  var DB_Process_Div = document.getElementById('DB_Process_Monitor');
                  DB_Process_Div.innerHTML += 'Result_Message : ACCOUNT STATUS LOCK<br>';
                  DB_Process_Div.innerHTML += 'Account ID : '+'".$ID."<br>';
                  DB_Process_Div.innerHTML += '틀린 횟수 : 5<br>';
                  DB_Process_Div.innerHTML += '메시지 : 계정이 잠겨있습니다. 관리자에게 문의하세요';
                </script>
              ";
              return "계정 잠금상태입니다.";
            }else if($Row_EXIST_ID[0] == 0){ //아이디, 비밀번호가 일치하는 계정이 존재하지 않고, 아이디 마저 일치하는 계정이 없을때
              echo
               "<script>
                  var DB_Process_Div = document.getElementById('DB_Process_Monitor');
                  DB_Process_Div.innerHTML += 'Result_Message : WRONG ID or PW<br>';
                  DB_Process_Div.innerHTML += 'POST ID : '+'".$ID."<br>';
                  DB_Process_Div.innerHTML += '메시지 : 아이디 또는 비밀번호가 틀렸습니다.';
                </script>
              ";
              return "아이디 또는 비밀번호가 틀렸습니다.";
            }
          }
        }
      }
      return "아이디 또는 비밀번호가 틀렸습니다.";
  }
  if(strlen($_POST['Id_Input'])>0 && strlen($_POST['Pass_Input'])>0){
    $Login_result = Login($_POST['Id_Input'],$_POST['Pass_Input']);
    echo "<script>alert('".$Login_result."');</script>";
  }

 ?>
