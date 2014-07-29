<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template class
 */
namespace RDev\Views\Pages;
use RDev\Tests\Views\Pages\Mocks;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tag placeholders */
    const TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS = "/templates/TestWithDefaultTagPlaceholders.html";
    /** The path to the test template with custom tag placeholders */
    const TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS = "/templates/TestWithCustomTagPlaceholders.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_PHP_CODE = "/templates/TestWithPHP.html";

    /**
     * Tests adding an uncallable function compiler
     */
    public function testAddingUncallableFunctionCompiler()
    {
        $this->setExpectedException("\\RuntimeException");
        $template = new Template();
        $template->addCompiler("foo");
    }

    /**
     * Tests getting the close tag when we've set it to a custom value
     */
    public function testGettingCustomCloseTag()
    {
        $template = new Template();
        $closeTag = "$$";
        $template->setCloseTagPlaceholder($closeTag);
        $this->assertEquals($closeTag, $template->getCloseTagPlaceholder());
    }

    /**
     * Tests getting the open tag when we've set it to a custom value
     */
    public function testGettingCustomOpenTag()
    {
        $template = new Template();
        $openTag = "^^";
        $template->setOpenTagPlaceholder($openTag);
        $this->assertEquals($openTag, $template->getOpenTagPlaceholder());
    }

    /**
     * Tests getting the close tag when it's set to the default value
     */
    public function testGettingDefaultCloseTag()
    {
        $template = new Template();
        $this->assertEquals($template::DEFAULT_CLOSE_TAG_PLACEHOLDER, $template->getCloseTagPlaceholder());
    }

    /**
     * Tests getting the open tag when it's set to the default value
     */
    public function testGettingDefaultOpenTag()
    {
        $template = new Template();
        $this->assertEquals($template::DEFAULT_OPEN_TAG_PLACEHOLDER, $template->getOpenTagPlaceholder());
    }

    /**
     * Tests rendering by setting the template path in the constructor
     */
    public function testRenderingBySpecifyingTemplatePathInConstructor()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $template->setTag("imSafe", "a&b");
        $compilerResult = $this->addCompiler($template);
        $this->assertEquals("Hello, world! {{blah}}. a&amp;b. c&amp;d. {{{\"e&f\"}}}. {{{blah}}}. Today is $compilerResult.",
            $template->render());
    }

    /**
     * Tests rendering a template that uses custom tag placeholders
     */
    public function testRenderingTemplateWithCustomTagPlaceholders()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS);
        $template->setOpenTagPlaceholder("^^");
        $template->setCloseTagPlaceholder("$$");
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $template->setTag("imSafe", "a&b");
        $compilerResult = $this->addCompiler($template);
        $this->assertEquals("Hello, world! ^^blah$$. a&amp;b. c&amp;d. {{{\"e&f\"}}}. {{{blah}}}. Today is $compilerResult.",
            $template->render());
    }

    /**
     * Tests rendering a template that uses the default tag placeholders
     */
    public function testRenderingTemplateWithDefaultTagPlaceholders()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $template->setTag("imSafe", "a&b");
        $compilerResult = $this->addCompiler($template);
        $this->assertEquals("Hello, world! {{blah}}. a&amp;b. c&amp;d. {{{\"e&f\"}}}. {{{blah}}}. Today is $compilerResult.",
            $template->render());
    }

    /**
     * Tests rendering a template with PHP code
     */
    public function testRenderingTemplateWithPHPCode()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH_WITH_PHP_CODE);
        $user1 = new Mocks\User(1, "foo");
        $user2 = new Mocks\User(2, "bar");
        $template->setTag("listDescription", "usernames");
        $template->setVar("users", [$user1, $user2]);
        $template->setVar("coolestGuy", "Dave");
        $compilerResult = $this->addCompiler($template);
        $this->assertEquals('List of usernames on ' . $compilerResult . ':
<ul>
    <li>foo</li><li>bar</li>
</ul> 2 items<br>Dave is a pretty cool guy. I agree.', $template->render());
    }

    /**
     * Tests rendering a template whose custom tags we didn't set
     */
    public function testRenderingTemplateWithUnsetCustomTags()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS);
        $template->setOpenTagPlaceholder("^^");
        $template->setCloseTagPlaceholder("$$");
        $compilerResult = $this->addCompiler($template);
        $this->assertEquals(", ! ^^blah$$. . c&amp;d. {{{\"e&f\"}}}. {{{blah}}}. Today is $compilerResult.", $template->render());
    }

    /**
     * Tests rendering a template whose tags we didn't set
     */
    public function testRenderingTemplateWithUnsetTags()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $compilerResult = $this->addCompiler($template);
        $this->assertEquals(", ! {{blah}}. . c&amp;d. {{{\"e&f\"}}}. {{{blah}}}. Today is $compilerResult.", $template->render());
    }

    /**
     * Tests that we cannot set the close then the open tags to the same thing as the safe tags
     */
    public function testSettingCloseThenOpenTagsToSafeTags()
    {
        $this->setExpectedException("\\RuntimeException");
        $template = new Template();
        $template->setCloseTagPlaceholder($template::SAFE_CLOSE_TAG_PLACEHOLDER);
        $template->setOpenTagPlaceholder($template::SAFE_OPEN_TAG_PLACEHOLDER);
    }

    /**
     * Tests setting multiple tags in a template
     */
    public function testSettingMultipleTags()
    {
        $template = new Template();
        $template->setTags(["foo" => "bar", "abc" => "xyz"]);
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($template);
        $this->assertEquals(["foo" => "bar", "abc" => "xyz"], $tags);
    }

    /**
     * Tests setting multiple variables in a template
     */
    public function testSettingMultipleVariables()
    {
        $template = new Template();
        $template->setVars(["foo" => "bar", "abc" => ["xyz"]]);
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($template);
        $this->assertEquals(["foo" => "bar", "abc" => ["xyz"]], $vars);
    }

    /**
     * Tests that we cannot set the open then the close tags to the same thing as the safe tags
     */
    public function testSettingOpenThenCloseTagsToSafeTags()
    {
        $this->setExpectedException("\\RuntimeException");
        $template = new Template();
        $template->setOpenTagPlaceholder($template::SAFE_OPEN_TAG_PLACEHOLDER);
        $template->setCloseTagPlaceholder($template::SAFE_CLOSE_TAG_PLACEHOLDER);
    }

    /**
     * Tests setting a tag in a template
     */
    public function testSettingSingleTag()
    {
        $template = new Template();
        $template->setTag("foo", "bar");
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($template);
        $this->assertEquals(["foo" => "bar"], $tags);
    }

    /**
     * Tests setting a variable in a template
     */
    public function testSettingSingleVariable()
    {
        $template = new Template();
        $template->setVar("foo", "bar");
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($template);
        $this->assertEquals(["foo" => "bar"], $vars);
    }

    /**
     * Tests setting the template path
     */
    public function testSettingTemplatePath()
    {
        $template = new Template();
        $template->setTemplatePath("foo");
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("templatePath");
        $property->setAccessible(true);
        $templatePath = $property->getValue($template);
        $this->assertEquals("foo", $templatePath);
    }

    /**
     * Adds a compiler to the template for use in testing
     *
     * @param Template $template The template to add the compiler to
     * @return string The expected result of the compiler
     */
    private function addCompiler(Template &$template)
    {
        $template->addCompiler(function ($content) use ($template)
        {
            return preg_replace($template->getFunctionMatcher("date"), "<?php echo $1->format('m/d/Y'); ?>", $content);
        });
        $today = new \DateTime("now", new \DateTimeZone("UTC"));
        $template->setVar("today", $today);

        return $today->format("m/d/Y");
    }
} 