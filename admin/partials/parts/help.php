
<?php
function print_help($self){
?>
    <h1>How to use</h1>
    <h3>[sayit]Blah blah[/sayit]</h3>
    <p>This is the shortcode you will use to transform "Blah blah" into a zone that will speak upon click</p>

    <h3>Parameters</h3>
    <ul>
        <li>lang - Use a language different from the default one</li>
        <li>speed - speed of speech (recommanded between 0.5 and 1.5)</li>
        <li>block - set to "1" to make it work on multuple paragraphes at once</li>
    </ul>
    <h3>Exemple :</h3>
    <p>
    [sayit block="1" lang="en-GB" speed="1"]&lt;p&gt;Hello I am the queen&lt;/p&gt;
    &lt;p&gt;And I talk for two paragraph long&lt;/p&gt;[/sayit]
    </p>

<?php } ?>