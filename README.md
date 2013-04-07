#SelectFile
==========
A select field to select a server file

This project was coded in 2007 with prototypeJS (http://www.prototypejs.org)

GitHub : https://github.com/funkymed/selectfile

##Demo

http://www.cyrilpereira.com/selectfile/

##Author

Cyril Pereira http://www.cyrilpereira.com

##Documentation

Add the select in your form
~~~
<form>
    <input type="hidden" id="file" name="file" readonly="readonly" />
    <div id="filemanager"></div>
</form>
~~~
End then initialize the code
~~~
<script type="text/javascript">
    var _SelectFile=new SelectFile(
    {
        SourceObjName:'_SelectFile',
        DivId:'filemanager',
        InputId:'file',
        ValidExtension:'.swf,.jpg',
        SourceDir:'directory/'
    });
</script>
~~~