<?php

    /**
     * Uses lex to parse content
     */
    include("lex/Parser.php");


    /**
     * Make sure Semantic UI is installed via the template
     * Javascript execution is expected to be done by the tempaltes js files.
     */
    class PlgContentSemantic extends JPlugin
    {
        /**
         * Plugin that cloaks all emails in content from spambots via Javascript.
         *
         * @param   string   $context  The context of the content being passed to the plugin.
         * @param   mixed    &$row     An object with a "text" property or the string to be cloaked.
         * @param   mixed    &$params  Additional parameters. See {@see PlgContentEmailcloak()}.
         * @param   integer  $page     Optional page number. Unused. Defaults to zero.
         *
         * @return  boolean True on success.
         */
        public function onContentPrepare($context, &$row, &$params, $page = 0)
        {
            // Fire up Lex
            $parser = new Lex\Parser();
            
            // Alter text
            $row->text = $parser->parse($row->text, array(), function($name, $attrs, $content){

                // By name
                switch($name){
                    
                    // Accordian
                    case 'accordion' : return $this->accordion($content);

                    default : return $content;
                }

            });
        }

        /**
         * Accordian Module
         *
         * Use within article:
         *
         * {{ accordion }}
         * {{ title active="true" }} What Are Weevils title? {{/title}}
         * {{ content active="true" }} This is what are Weevils content {{/content}}
         * {{ /accordion }}
         *
         */
        private function accordion($text)
        {
            // Fire up new lex
            $parser = new Lex\Parser();
            $data[] = $parser->parse($text, array(), function($name, $attrs, $content){

                // Check for active accordian
                if(isset($attrs['active']) and $attrs['active'] == true){
                    $active = "active ";
                }

                // Switch between title and content
                switch($name){
                    case 'title' : return '<div class="'.(isset($active) ? $active : null).'title"><i class="dropdown icon"></i>'.strip_tags(trim($content)).'</div>';
                    case 'content' : return '<div class="'.(isset($active) ? $active : null).'content">'.trim($content).'</div>';
                }

            });

            // Join each accordian
            $data = array(
                '<div class="ui accordion">',
                implode($data),
                '</div>'
            );

            // Return combined data
            return implode($data);
        }
    }