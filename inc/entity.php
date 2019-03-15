<?php

//vmember und blanklist müssen noch dokumentiert werden
interface vmember_documentation
{
}

interface blanklist_documentation
{
}


class vmember implements Iterator, vmember_documentation
{
   
   protected $allowed=array(); //es werden nur virtuelle member (dh. $vmember_instance->name) erstellt, deren member-name in diesem array zu finden ist.
                               //Will man einen Variablennamen für eine virtuelle Membervariable freischalten, muss man ihn nur diesem Array hinzufügen.
                               //Es ist nicht empfohlen, vmember direkt zu benutzen. Stattdessen sollte man von dieser Klasse ableiten und $allowed 
                               //überschreiben.
   protected $only_allowed=true; //true: $allowed wird benutzt, siehe oben. false: $allowed wird ignoriert, alle membernamen sind zulässig.

   private $daten;
   private $iterator;

   function __construct($array=null)
   {
      if(!isset($array))
         $array=array();
      $this->daten=new ArrayObject(array());
      foreach($array as $key=>$value)
         $this->$key=$value;
      $this->iterator=$this->daten->getIterator();
      if (!$this->only_allowed)
      {
         $classname=get_class($this);
         trigger_error("Possible security issue in class $classname: There is no restriction on virtual member names. To define restrictions
                        set \$only_allowed=true and allow virtual member names in \$allowed=array('membervar1', 'membervar2',...) in your class    
                        description. For more information read the documentation in class vmember ",E_USER_NOTICE);
      }
   }
   
   function get_copy()
   {
      $class=get_class($this);
      $copy=new $class($this->daten->getArrayCopy());
      return $copy;
   }
   
   function kopie()
   {
      return $this->get_copy();
   }
   
   //klasse ausgeben für debugzwecke
   function __toString()
   {
      $s=get_class($this).':';
      foreach($this->daten as $key=>$value)
         $s.="<br />$key=$value";
      return $s;
   }
   
   //virtuelle membervariablen
   function __set($name, $value)
   {
      if ($this->only_allowed)
         if (!in_array($name,$this->allowed))
         {
            $classname=get_class($this);
            trigger_error("Could not create virtual member $name in $classname, access denied", E_USER_WARNING);
            return;
         }
      $this->daten[$name]=$value;
   }

   function __get($name)
   {
      if (!isset($this->daten[$name]))
      {
         $class=get_class($this);
         trigger_error("Undefined Index $name in class $class",E_USER_NOTICE);
         return null;
      }
      return $this->daten[$name];
   }

   function __isset($name)
   {
      return isset($this->daten[$name]);
   }

   function __unset($name)
   {
      unset($this->daten[$name]);
   }
   
   //Iterator
   public function current()
   {
      return $this->iterator->current();
   }

   public function key()
   {
      return $this->iterator->key();
   }

   public function next()
   {
      $this->iterator->next();
   }

   public function rewind()
   {
      $this->iterator=$this->daten->getIterator();
      $this->iterator->rewind();
   }

   public function valid()
   {
      return $this->iterator->valid();
   }
}


class blanklist implements ArrayAccess,Iterator, blanklist_documentation
{
   private $size=0;
   private $daten;
   private $key;
   private $items_per_page;
   private $page=1;


   public function add(vmember $zeile)
   {
      $this->daten[]=$zeile;
      $this->size=count($this->daten);
      $this->items_per_page=$this->size;
   }

   public function clear()
   {
      unset($this->daten);
      $this->size=0;
      $this->items_per_page=$this->size;
   }


   function get_size()
   {
      return $this->size;
   }
   
   function size()
   {
      return $this->size;
   }

   //ArrayAccess
   function offsetExists($offset)
   {
      $offset=($this->page-1)*$this->items_per_page+$offset;
      return isset($this->daten[$offset]);
   }

   function offsetGet($offset)
   {
      $offset=($this->page-1)*$this->items_per_page+$offset;
      return $this->daten[$offset];
   }

   function offsetSet($offset, $value)
   {
      $offset=($this->page-1)*$this->items_per_page+$offset;
      $this->daten[$offset]=$value;
   }

   function offsetUnset($offset)
   {
      $offset=($this->page-1)*$this->items_per_page+$offset;
      unset($this->daten[$offset]);
   }
   
   //Iterator
   public function current()
   {
      return $this->daten[$this->key];
   }

   public function key()
   {
      return $this->key;
   }

   public function next()
   {
      $this->key++;
   }

   public function rewind()
   {
      $this->key=0;
   }

   public function valid()
   {
      return isset($this->daten[$this->key]);
   }
}
?>
