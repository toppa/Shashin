<?php

class Lib_ShashinHtmlFormField {
    private $name = "";
    private $id = "";
    private $value = "";
    private $cssClass = "";
    private $tag = "";
    private $tagGroup = "";
    private $refData = array(
        'col_params' => array('length' => null), // used for text input maxlength
        'input' => array(
            'type' => null, // determines type of input
            'size' => null, // for text input
            'subgroup' => array(), // labels and values for radio buttons, checkboxes, and select field options
            'cols' => null, // for textarea
            'rows' => null) // for textarea
    );

    public function __construct($name, array $refData) {
        $this->id = str_replace("[", "_", $name);
        $this->id = str_replace("]", "", $this->id);
        $this->name = $name;
        $this->refData = $refData;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this->value;
    }

    public function setCssClass($cssClass) {
        $this->cssClass = $cssClass;
        return $this->cssClass;
    }

    public function build() {
        // clear any previously built tags
        $this->tag = "";
        $this->tagGroup = "";

        switch ($this->refData['input']['type']) {
        case 'text':
            return $this->buildTextField();
            break;
        case 'password':
            return $this->buildPasswordField();
            break;
        case 'hidden':
            return $this->buildHiddenField();
            break;
        case 'radio':
            return $this->buildRadioGroup();
            break;
        case 'select':
            return $this->buildSelectField();
            break;
        case 'textarea':
            return $this->buildTextarea();
            break;
        case 'checkbox':
            return $this->buildCheckboxGroup();
            break;
        }

        return false;
    }

    public static function quickBuild($name, array $refData, $value = null, $cssClass = null) {
        $field = new Lib_ShashinHtmlFormField($name, $refData, $value, $cssClass);

        if ($value) {
            $field->setValue($value);
        }

        if ($cssClass) {
            $field->setCssClass($cssClass);
        }

        return $field->build();
    }

    private function buildTextField() {
        $this->startTag('input');
        $this->addAttribute('type', 'text');
        if (isset($this->name))
            $this->addAttribute('name', $this->name);
        if (isset($this->id))
            $this->addAttribute('id', $this->id);
        if (isset($this->value))
            $this->addAttribute('value', $this->value);
        if (isset($this->refData['input']['size']))
            $this->addAttribute('size', $this->refData['input']['size']);
        if (isset($this->cssClass))
            $this->addAttribute('class', $this->cssClass);
        if (isset($this->refData['db']['length']))
            $this->addAttribute('maxlength', $this->refData['db']['length']);
        $this->selfCloseTag();
        return $this->tag;
    }

    private function buildHiddenField() {
        $this->startTag('input');
        $this->addAttribute('type', 'hidden');
        if (isset($this->name))
            $this->addAttribute('name', $this->name);
        if (isset($this->id))
            $this->addAttribute('id', $this->id);
        if (isset($this->value))
            $this->addAttribute('value', $this->value);
        if (isset($this->refData['input']['size']))
            $this->addAttribute('size', $this->refData['input']['size']);
        if (isset($this->cssClass))
            $this->addAttribute('class', $this->cssClass);
        if (isset($this->refData['db']['length']))
            $this->addAttribute('maxlength', $this->refData['db']['length']);
        $this->selfCloseTag();
        return $this->tag;
    }

    private function buildPasswordField() {
        $this->startTag('input');
        $this->addAttribute('type', 'password');
        if (isset($this->name))
            $this->addAttribute('name', $this->name);
        if (isset($this->id))
            $this->addAttribute('id', $this->id);
        if (isset($this->value))
            $this->addAttribute('value', $this->value);
        if (isset($this->refData['input']['size']))
            $this->addAttribute('size', $this->refData['input']['size']);
        if (isset($this->cssClass))
            $this->addAttribute('class', $this->cssClass);
        if (isset($this->refData['db']['length']))
            $this->addAttribute('maxlength', $this->refData['db']['length']);
        $this->selfCloseTag();
        return $this->tag;
    }

    private function buildRadioGroup() {
        foreach ($this->refData['input']['subgroup'] as $value=>$label) {
            $this->startTag('input');
            $this->addAttribute('type', 'radio');
            if (isset($this->name))
                $this->addAttribute('name', $this->name);
            if (isset($this->id))
                $this->addAttribute('id', $this->id . '_' . str_replace(' ', '_', $value));
            if (isset($this->value))
                $this->addAttribute('value', $value);
            if (isset($this->cssClass))
                $this->addAttribute('class', $this->cssClass);
            if (isset($this->value))
                $this->addChecked($value, $this->value);
            $this->selfCloseTag($label);
            $this->tagGroup .= $this->tag;
        }

        return $this->tagGroup;
    }

    private function buildSelectField() {
        $this->startTag('select');
        if (isset($this->name))
            $this->addAttribute('name', $this->name);
        if (isset($this->id))
            $this->addAttribute('id', $this->id);
        if (isset($this->cssClass))
            $this->addAttribute('class', $this->cssClass);
        $this->closeTag();
        $this->tagGroup .= $this->tag;

        foreach ($this->refData['input']['subgroup'] as $value=>$label) {
            $this->startTag('option');
            $this->addAttribute('value', $value);
            $this->addSelected($value, $this->value);
            $this->closeTag($label);
            $this->addClosingTag('option');
            $this->tagGroup .= $this->tag;
        }
        $this->tag = ''; // clears out last option that was set
        $this->addClosingTag('select');
        $this->tagGroup .= $this->tag;
        return $this->tagGroup;
    }

    private function buildTextarea() {
        $this->startTag('textarea');
        if (isset($this->name))
            $this->addAttribute('name', $this->name);
        if (isset($this->id))
            $this->addAttribute('id', $this->id);
        if (isset($this->cssClass))
            $this->addAttribute('class', $this->cssClass);
        if (isset($this->refData['input']['cols']))
            $this->addAttribute('cols', $this->refData['input']['cols']);
        if (isset($this->refData['input']['rows']))
            $this->addAttribute('rows', $this->refData['input']['rows']);
        if (isset($this->value)) {
            $this->closeTag(htmlspecialchars($this->value));
        }

        else {
            $this->closeTag();;
        }

        $this->addClosingTag('textarea');
        return $this->tag;
    }

    private function buildCheckboxGroup() {
        foreach ($this->refData['input']['subgroup'] as $value=>$label) {
            $this->startTag('input');
            $this->addAttribute('type', 'checkbox');
            if (isset($this->name))
                $this->addNameAsArrayField('name', $this->name);
            if (isset($this->id))
                $this->addAttribute('id', $this->id . '_' . str_replace(' ', '_', $value));
            if (isset($this->value))
                $this->addAttribute('value', $value);
            if (isset($this->cssClass))
                $this->addAttribute('class', $this->cssClass);
            if (isset($this->value))
                $this->addCheckedForArrayValue($value, $this->value);
            $this->selfCloseTag($label);
            $this->tagGroup .= $this->tag;
        }

        return $this->tagGroup;
    }

    private function startTag($tag, $label = null) {
        if ($label) {
            $this->tag = "$label <$tag";
        }

        else {
            $this->tag = "<$tag";
        }
    }

    private function selfCloseTag($label = null) {
        $this->tag .= " />";

        if ($label) {
            $this->tag .= " $label";
        }

        $this->tag .= PHP_EOL;
    }

    private function closeTag($text = null) {
        $this->tag .= ">$text";
    }

    private function addClosingTag($tag) {
        $this->tag .= "</$tag>" . PHP_EOL;
    }

    private function addAttribute($type, $value = null) {
       if (is_null($value)) {
            return null;
       }

        $this->tag .= " $type=\"" . htmlspecialchars($value) . '"';
    }

    private function addNameAsArrayField($name, $value = null) {
        $value .= '[]';
        return $this->addAttribute($name, $value);
    }

    private function addChecked($default, $value) {
        if ($default == $value) {
            $this->tag .= ' checked="checked"';
        }
    }

    private function addCheckedForArrayValue($default, $value) {
        if ($value && in_array($default, $value)) {
            $this->tag .= ' checked="checked"';
        }
    }

    private function addSelected($default, $value) {
        if ($default == $value) {
            $this->tag .= ' selected="selected"';
        }
    }
}
