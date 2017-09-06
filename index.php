<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>The Smart Calculator</title>
    </head>
    <body>
        <form action="" method="post">
        
            <fieldset>
                <legend>Calculator:</legend>
                <h4>Notes</h4>
                <ul>
                    <li>Factorials (!) are not supported</li>
                    <li>Square Root (sqrt()) is not supported</li>
                    <li>Pi (π) must be manually typed <br> π = 3.1415926535897932384626433832795 <br> We recommend using 3.141592 for most calculations</li>
                </ul>
                <p><label>Equation: <input type="text" name="equation" size="120" value="<?php if(isset($_POST['algorithm'])) echo $_POST['algorithm']; ?>"/></label></p>
                <p><Label>Algorithm: <select name="algorithm">
                            <option value=''>Select Algorithm</option>
                            
                <?php  
                    DEFINE('DB_USER', 'root');
                    DEFINE('DB_PASSWORD', '');
                    DEFINE('DB_HOST', 'localhost');
                    DEFINE('DB_NAME', 'final_project');

                    //Connect
                    $dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );

                    //Encoding
                    mysqli_set_charset($dbc, 'utf8');        
                    
                    $query = "SELECT algorithmName, algorithm FROM algorithms";
                    $result = mysqli_query($dbc, $query);
                    
                    $row = $result->fetch_all(MYSQLI_NUM);
                    $count = count($row)-1;
                    //echo "<select name='algorithm'>";
                    //echo "<option value=''>Select Algorithm</option>";
                    while ($count >= 0) {
                        echo "<option value=" . $row[$count][1] . ">" . $row[$count][0] . "</option>";
                        $count--;
                    }
                    //echo "</select>";
                ?>
                        </select></p>
                        
            </fieldset>
            <p><input type="submit" name="Submit" value="Calculate or Load Algorithm"/></p>
        
        </form>
        
        
        <?php
        error_reporting(E_ERROR);
        
        //Finds the numbers surrounding an operator
        function findBeforeAndAfter($string, $j)
        {
            //echo "Enter FBAA <br>";
            $before = "";
            $after = "";
            $end=false;
            $k = $j-1;
            $afterPos;
            $beforePos;
            while (substr($string, $k, 1) != "*" && substr($string, $k, 1) != "/" && substr($string, $k, 1) != "+" && substr($string, $k, 1) != "-" && substr($string, $k, 1) != "%")
            {
                //echo "FBAA - While_1 K=" . $k . "<br>";
                //echo "FBAA - While_1 K_Value = " . substr($string, $k, 1) . "<br>";
                if($k == 0)
                {
                    //echo "FBAA - While_1 K=Start" . "<br>";
                    $end = true;
                    break;
                }
                $k--;
                if($k == 0)
                {
                   //echo "FBAA - While_1 If_2 K=Start" . "<br>";
                   $end = true;
                   break;
                }
            }
            if($end == true)
            {
                $before = substr($string, $k, $j);
                $beforePos = $k;
            }
            else
            {
                $before = substr($string, $k+1, $j-$k-1);
                $beforePos = $k+1;
            }

            $k = $j+1;
            $end = false;
            while (substr($string, $k, 1) != "*" && substr($string, $k, 1) != "/" && substr($string, $k, 1) != "+" && substr($string, $k, 1) != "-" && substr($string, $k, 1) != "%")
            {
                //echo "FBAA - While_2 K=" . $k . "<br>";
                if($k == strlen($string)-1)
                {
                    //echo "FBAA - While_2 K=Length" . "<br>";
                    $end = true;
                    break;
                }
                $k++;
            }
            if($end == true)
            {
                $after = substr($string, $j+1, $k-$j);
                $afterPos = -1;
            }
            else
            {
                $after = substr($string, $j+1, $k-$j);
                $afterPos = $k;
            }
            //echo "before = " . $before . "<br> ";
            //echo "beforePos = " . $beforePos . "<br> ";
            //echo "After = " . $after . "<br> ";
            //echo "AfterPos = " . $afterPos . "<br> ";
            return array($before, $beforePos, $after, $afterPos);
        }
        
        //Finds the inner most parenthesis and solves them, then calls itself to remove all other parenthesis
        function simplifyparenthesis($string)
        {
            //echo "Enter Simplify Parentheses" . "<br>";
            $bPos = 0;
            $aPos = -1;

            for($j = 1; $j < strlen($string); $j++)
            {
                //echo "Simplify Parentheses - Char: " . substr($string, $j, 1) . "<br>";
                //echo "Simplify Parentheses - Position: " . substr($string, $j, 1) . "<br>";
                if(substr($string, $j, 1) == "(")
                {
                    $bPos = $j;
                }
                elseif(substr($string, $j, 1) == ")")
                {
                    $aPos = $j;
                    break;
                }
            }
            //echo "Enter Simplify Parentheses bPos = " . $bPos . "<br>";
            //echo "Enter Simplify Parentheses aPos = " . $aPos . "<br>";

            if($aPos == -1)
            {
                return "ERR - No closing Parentheses";
            }
            $newString = substr($string, $bPos+1, $aPos-$bPos-1);
            if(substr($newString, 0, 1) == "+" || substr($newString, 0, 1) == "-" || substr($newString, 0, 1) == "*" || substr($newString, 0, 1) == "/" || substr($newString, 0, 1) == "%" || substr($newString, 0, 1) == "^" || substr($newString, strlen($newString)-1, 1) == "+" || substr($newString, strlen($newString)-1, 1) == "-" || substr($newString, strlen($newString)-1, 1) == "*" || substr($newString, strlen($newString)-1, 1) == "/" || substr($newString, strlen($newString)-1, 1) == "%" || substr($newString, strlen($newString)-1, 1) == "^")
            {
                return "ERROR - Cannot Calculate | Missing Number After Operator  <br>"; 
            }
            //echo "Enter Simplify Parentheses newString = " . $newString . "<br>";

            $innerSolved = simplifyExponent($newString);

            //echo "Enter Simplify Parentheses innerSolved = " . $innerSolved . "<br>";

            if($bPos == 0)
            {
                $newString = $innerSolved;
            }
            else
            {
                //echo "Simplify Parentheses PassedString = " . substr($string, 0, $bPos) . $innerSolved . substr($string, $aPos+1) . "<br>";
                $newString = simplifyparenthesis(substr($string, 0, $bPos) . $innerSolved . substr($string, $aPos+1));   
            }
            return $newString;
        }
        
        //Solves Exponents
        function simplifyExponent($string)
        {
            //echo "Enter Simplify Exponent" . "<br>";
            //echo "Enter Simplify Exponent - String: " . $string . "<br>";
            for($j = 0; $j < strlen($string); $j++)
            {
                //echo "Simplify Exponent - J=" . $j . "<br>";
                //echo "Character: - " . substr($string, $j, 1) . "<br>";
                if(substr($string, $j, 1) == "^")
                {
                    //echo "Simplify Exponent - Enter If ^" . "<br>";
                    //echo "Simplify Exponent - String Passed: " . $string . "<br>";
                    $parsedEquation = findBeforeAndAfter($string, $j);
                    $num1 = $parsedEquation[0];
                    $num2 = $parsedEquation[2];
                    $beforePos = $parsedEquation[1];
                    $afterPos = $parsedEquation[3];               
                    $finalNum = pow($num1, $num2);   
                    if($beforePos == -1)
                    {
                        return null;
                    }
                    if($afterPos == -1)
                    {
                        $newString = substr($string, 0, $beforePos) . $finalNum;
                    }
                    else
                    {
                        $newString = substr($string, 0, $beforePos) . $finalNum . substr($string, $afterPos);
                    }
                    return basicMath($newString);
                }
            }
            return basicMath($string);
        }

        //Solves  = - * / and % equations
        function basicMath($string)
        {
            //echo "Enter Basic Math" . "<br>";
            for($j = 0; $j < strlen($string); $j++)
            {
                //echo "Basic Math - J=" . $j . "<br>";
                //echo "Character: - " . substr($string, $j, 1) . "<br>";
               if(substr($string, $j, 1) == "/" || substr($string, $j, 1) == "*" || substr($string, $j, 1) == "%")
               {
                   //echo "Basic Math - Enter If_Divide" . "<br>";
                    // Array Structure
                    // Before, beforePos, After, afterPos
                    $parsedEquation = findBeforeAndAfter($string, $j);
                    $num1 = $parsedEquation[0];
                    $num2 = $parsedEquation[2];
                    $beforePos = $parsedEquation[1];
                    $afterPos = $parsedEquation[3];
                    //Error Checking
                    if($beforePos == -1)
                    {
                        return null;
                    }

                    if(substr($string, $j, 1) == "/")
                    {
                        $finalNum = $num1/$num2;
                    }
                    elseif(substr($string, $j, 1) == "*")
                    {
                        $finalNum = $num1*$num2;
                    }
                    elseif(substr($string, $j, 1) == "%")
                    {
                        $finalNum = $num1%$num2;
                    }
                    //Error Checking
                    else 
                    {
                        echo "INTERNAL ERROR - Report to Developer | Code: 319  <br>";
                    }

                    if($afterPos == -1)
                    {
                        $newString = substr($string, 0, $beforePos) . $finalNum;
                    }
                    else
                    {
                        $newString = substr($string, 0, $beforePos) . $finalNum . substr($string, $afterPos);
                    }
                    $string = $newString;
                    $j = 0;
               }
            }
            for($j = 0; $j < strlen($string); $j++)
            {
                if(substr($string, $j, 1) == "+" || substr($string, $j, 1) == "-")
                {
                    //echo "Basic Math - Enter If_Add" . "<br>";
                    //echo "Basic Math - String Passed: " . $string . "<br>";
                     // Array Structure
                     // Before, beforePos, After, afterPos
                     $parsedEquation = findBeforeAndAfter($string, $j);
                     $num1 = $parsedEquation[0];
                     $num2 = $parsedEquation[2];
                     $beforePos = $parsedEquation[1];
                     $afterPos = $parsedEquation[3];

                     if(substr($string, $j, 1) == "+")
                     {
                         $finalNum = $num1+$num2;
                     }
                     elseif(substr($string, $j, 1) == "-")
                     {
                         $finalNum = $num1-$num2;
                     }
                     else 
                     {
                         echo "INTERNAL ERROR - Report to Developer | Code: 318  <br>";
                     }

                     if($afterPos == -1)
                     {
                         $newString = substr($string, 0, $beforePos) . $finalNum;
                     }
                     else
                     {
                         $newString = substr($string, 0, $beforePos) . $finalNum . substr($string, $afterPos);
                     }
                     $string = $newString;
                     $j = 0;
                }
            }
            //Should be a single number (All math of any kind is solved already)
            //echo "Basic Math - String = " . $string . "<br>";
            return $string;

        }
      
        if(!empty($_POST['equation']) && $_POST['algorithm'] == '')
        {
            //Clean Data & Save to Variable
            $equation = $_POST['equation'];
            
            //Check for variables (All variables must be filled in)         
            if(preg_match("#[a-z]#", $equation) || preg_match("#[A-Z]#", $equation))
            {
                echo "Must fill in all variables | Equation cannot contain A-Z <br>";
            }
            
            $solved = false;
            $answer = "";
            //Error Checking
            if(substr($equation, 0, 1) == "+" || substr($equation, 0, 1) == "-" || substr($equation, 0, 1) == "*" || substr($equation, 0, 1) == "/" || substr($equation, 0, 1) == "%" || substr($equation, 0, 1) == "^" || substr($equation, strlen($equation)-1, 1) == "+" || substr($equation, strlen($equation)-1, 1) == "-" || substr($equation, strlen($equation)-1, 1) == "*" || substr($equation, strlen($equation)-1, 1) == "/" || substr($equation, strlen($equation)-1, 1) == "%" || substr($equation, strlen($equation)-1, 1) == "^")
            {
                $answer = "ERROR - Cannot start or end an equation with an operator <br>";
                $solved = true;
            }
            
            //Search for Parentheses
            if($solved == false)
            {    
                for($i = 0; $i < strlen($equation); $i++)
                {
                    if(substr($equation, $i, 1) == "(")
                    {
                        $answer = simplifyparenthesis("(" . $equation . ")");
                        $solved = true;
                        $i = strlen($equation);
                    }
                }
            }
            //Search for Exponent
            if($solved == false)
            {
                //echo "Enter Search Exponent - String: " . $equation . "<br>";
                for($i = 0; $i < strlen($equation); $i++)
                {
                    //echo "Enter Search Exponent - i=" . $i . "<br>";
                    if(substr($equation, $i, 1) == "^")
                    {
                        //echo "Enter Search Exponent - Passed String: " . $equation . "<br>";
                        $answer = simplifyExponent($equation);
                        $solved = true;
                        $i = strlen($equation);
                    }
                }
            }
            
            //Search for Basic Math
            if($solved == false)
            {
                //echo "Call Basic Math <br> ";
                $answer = basicMath($equation);
            }
            echo $equation . " = " . $answer;
            
          
        }
        
      
        
        ?>
    </body>
</html>