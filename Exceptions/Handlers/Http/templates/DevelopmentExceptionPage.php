<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Something went wrong</title>
        <style>
            body
            {
                font-family: arial, sans-serif;
                font-size: 14px;
            }

            h1, h2, h3, h4, h5, h6
            {
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <h2>Something went wrong</h2>
        <?php echo htmlentities($ex->getMessage(), ENT_QUOTES, "UTF-8"); ?>
    </body>
</html>