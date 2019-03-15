//Erzeugt transparenten Hintergrund für beiebige Elemente
function transparent(eid, opacity)
{
   var e=document.getElementById(eid);
   if (e==null)
   {
      produceError('transparent: Could not find id '+eid);
      return;
   }
   var bglayer=document.getElementById('transparent'+eid);
   if (bglayer!=null)
      return;
         
   if (opacity==null)
      opacity=0.7;
      
   var bglayer=document.createElement('div');
   bglayer.style.position='absolute';
   bglayer.style.opacity=opacity;
   bglayer.style.backgroundColor='#FFFFFF';
   bglayer.style.zIndex=-1;
   bglayer.style.height=e.clientHeight+'px';
   bglayer.style.width=e.clientWidth+'px';
   bglayer.id='transparent'+eid;
   if (e.hasChildNodes())
      e.insertBefore(bglayer, e.firstChild);
   else
      e.appendChild(bglayer);
}
