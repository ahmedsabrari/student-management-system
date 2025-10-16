<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <title></title>
    <style>
        body{
            background-color: whitesmoke;
            font-family: "Tajawal", sans-serif;
        }
        #mother{
            width: 100%;
            font-size: 20px;
        }
        main{
            float: right;
            border: 1px solid gray;
            padding: 5px;
        }
        input{
            padding: 4px;
            border: 2px solid black;
            text-align: center;
            font-size: 17px;
            font-family: "Tajawal", sans-serif;
        }
        aside{
            text-align: center;
            width: 400PX;
            float: left;
            border: 1px solid black;
            padding: 10x;
            font-size: 20px;
            background-color: silver;
            color: white;
        }
        #tbl{
            width: 900px;
            font-size: 20px;
        }
        #tbl th{
            background-color: silver;
            color: black;
            font-size: 20px;
            padding: 10px;
        }
        aside button{
            width: 190px;
            padding: 8px;
            margin-top: 7px;
            font-size: 17px;
            font-family: "Tajawal", sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="mother">
        <form action="" method="post">
            <!-- Control panel -->
            <aside>
                <div id="dev">
                    <img src="./img/privo-circle-kid-5.webp" alt="logo site" width="200">
                    <h3>Admin panel</h3>
                    <label for="id">Student number:</label><br>
                    <input type="text" name="id" id="id"><br>

                    <label for="name">Student name</label><br>
                    <input type="text" name="name" id="name"><br>

                    <label for="address">Student address</label><br>
                    <input type="text" name="address" id="address"><br><br>

                    <button name="add"> addend Student</button>
                    <button name="del"> delete Student</button>
                </div>
            </aside>
            <!-- View students data -->
            <main>
                <table id="tbl">
                    <tr>
                        <th>Serial number</th>
                        <th>Student name </th>
                        <th>Student address</th>
                    </tr>
                    <tr>

                    </tr>
                </table>
            </main>
        </form>
    </div>
</body>
</html>