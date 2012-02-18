<?php
namespace Potherca
{

    class Template extends \DOMDocument
    {
        /**
         * @var \DomXPath
         */
        protected $m_oFinder;

        /**
         * @return \DomXPath
         */
        public function getFinder()
        {
            if(!isset($this->m_oFinder))
            {
                $this->m_oFinder = new \DomXPath($this);
            }#if

            return $this->m_oFinder;
        }

        /**
         * @return \DOMNode
         */
        public function getHead()
        {
            return $this->getElementsByTagName('head')->item(0);
        }

        /**
         * @return \DOMNode
         */
        public function getBody()
        {
            return $this->getElementsByTagName('body')->item(0);
        }

        /**
         * @param string $p_sFile
         *
         * @return Template
         */
        static public function fromFile($p_sFile)
        {
            $oTemplate = new static();
            $oTemplate->loadHTMLFile($p_sFile);
            return $oTemplate;
        }

        /**
         * @param $p_sClassName
         *
         * @return \DOMNodeList
         */
        public function getElementsByClassName($p_sClassName)
        {
            //@FIXME: The XPath is not stringent enough. If you look for class 'foo' then class "bar foobar" will also be returned
            return $this->getFinder()->query("//*[contains(@class, '$p_sClassName')]");
        }

        /**
         * @param \DOMNode $DomNode
         */
        function removeChildrenFromNode(\DOMNode $DomNode)
        {
            if ($DomNode->hasChildNodes()) {
                $childNodes = $DomNode->childNodes;

                while ($childNodes->length > 0) {
                    $DomNode->removeChild($childNodes->item(0));
                }#while
            }#if
        }

        /**
         * @return string
         */
        public function __toString() {
            return $this->toString();
        }

        /**
         * @return string
         */
        public function toString()
        {
            return $this->saveHTML();
        }
    }
}

#EOF
