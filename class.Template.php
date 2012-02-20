<?php

class Template extends DOMDocument
{
    /**
     * @var DomXPath
     */
    protected $m_oFinder;

    /**
     * @return DomXPath
     */
    public function getFinder()
    {
        if(!isset($this->m_oFinder))
        {
            $this->m_oFinder = new DomXPath($this);
        }#if

        return $this->m_oFinder;
    }

    /**
     * @return DOMElement
     */
    public function getHead()
    {
        return $this->getElementsByTagName('head')->item(0);
    }

    /**
     * @return DOMElement
     */
    public function getBody()
    {
        return $this->getElementsByTagName('body')->item(0);
    }

    /**
     * @param string $p_sFile
     *
     * @return self
     */
    static public function fromFile($p_sFile)
    {
        $oTemplate = new static();
        $oTemplate->formatOutput = true;
        $oTemplate->loadHTMLFile($p_sFile);
        return $oTemplate;
    }

    /**
     * @param $p_sClassName
     *
     * @return DOMElement|null
     */
    public function getFirstElementWithClassName($p_sClassName)
    {
        $node = null;
        $DOMNodeList = $this->getElementsByClassName($p_sClassName);
        if($DOMNodeList->length > 0){
            $node = $DOMNodeList->item(0);
        }

        return $node;
    }

    /**
     * @param $p_sClassName
     *
     * @return DOMNodeList
     */
    public function getElementsByClassName($p_sClassName)
    {
        //@FIXME: The XPath is not stringent enough. If you look for class 'foo' then class "bar foobar" will also be returned
        return $this->getFinder()->query("//*[contains(@class, '$p_sClassName')]");
    }

    /**
     * @param DOMElement $DomNode
     */
    function removeChildrenFromNode(DOMElement $DomNode)
    {
        //@TODO: Add removed children to a DOMNodeList and return that.
        if ($DomNode->hasChildNodes()) {
            $childNodes = $DomNode->childNodes;

            while ($childNodes->length > 0) {
                $DomNode->removeChild($childNodes->item(0));
            }#while
        }#if
    }

    /**
     * @param string $attributeName
     * @param string $value
     * @param array  $attributes
     *
     * @return DOMElement
     */
    public function createElementWithAttributes($attributeName, $value = null, array $attributes)
    {
        $DomElement = $this->createElement($attributeName, $value);

        foreach($attributes as $attributeName => $attributeValue){
            $DomElement->setAttribute($attributeName, $attributeValue);
        }#foreach

        return $DomElement;
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

#EOF
