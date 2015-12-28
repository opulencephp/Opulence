<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
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

            ul.errors
            {
                list-style: disc inside none;
            }
        </style>
    </head>
    <body>
        <h2>Something went wrong</h2>
        <ul class="errors">
            <li>
                <pre><?php echo htmlentities($ex->getMessage(), ENT_QUOTES, "UTF-8"); ?></pre>
            </li>
            <?php while ($ex = $ex->getPrevious()): ?>
                <li>
                    <pre><?php echo htmlentities($ex->getMessage(), ENT_QUOTES, "UTF-8"); ?></pre>
                </li>
            <?php endwhile; ?>
        </ul>
    </body>
</html>