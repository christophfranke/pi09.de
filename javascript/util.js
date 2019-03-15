//Einige diverse Funktionen, die immer mal gebraucht werden

function idle()
{
}

//ohne value parameter: gibt ein array aller kinder von <node> zurück, die das Attribut <attribute> besitzen
//mit value parameter: gibt ein array aller kinder von <node> zurück, deren Attribut <attribute> den wert <value> besitzen
//der parameter found ist nur für interne zwecke
function findAllTags(node, attribute, value, found)
{
   //noch nichts gefunden
   if (found == undefined || found == null)
      found = new Array();
      
   //ungültiger knoten
   if (node == undefined || node == null)
      return found;

   //ok, gefunden
   if (node.hasAttribute(attribute) && (value==null || node.getAttribute(attribute)==value))
      found.push(node);
      
   //und weitersuchen
   var children = node.childNodes;
   for (var i=0;i<children.length;i++)
      if (children[i].nodeType==1)
         findAllTags(children[i], attribute, value, found);
   
   //alle kinder durchlaufen, gefundenen Knoten zurückgeben
   return found;
}

//parameter genau wie findAllTags, gibt aber nur den ersten gefundenen Knoten zurück
function findTag(node, attribute, value)
{
   //ungültiger knoten
   if (node == undefined || node == null)
      return false;
      
   if (value == undefined)
      value = null;
      
   //ok, gefunden
   if (node.hasAttribute(attribute) && (value==null || node.getAttribute(attribute)==value))
      return node;
      
   //und weitersuchen
   var result = false;
   var children = node.childNodes;
   for (var i=0;i<children.length;i++)
      if (children[i].nodeType==1)
      {
         result = findTag(children[i], attribute, value);
         if (result != false)
            return result;
      }
   
   //alle kinder durchlaufen und nix gefunden
   return false;
}
