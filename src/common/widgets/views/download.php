<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <input type="hidden" name="attachment_id" value="{%=file.id%}" />
        <td>
            <span class="preview">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.url%}" width="100px" height="60px"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.url?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger"><?= Yii::t('fileupload', 'Error') ?></span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <span class="">名称：<input type="text" name="attachment_filename[{%=file.id%}]" value="{%=file.filename%}"/></span>
        </td>
        <td>
            <span class="">置为主图：<input type="checkbox" name="attachment_is_master[{%=file.id%}]" value="1" {% if (file.is_master) { %} checked="checked" {% } %} onclick="inputChecked($(this))" /></span>
        </td>
        <td>
            <span class="size">排序：<input type="text" name="attachment_orderlist[{%=file.id%}]" value="{%=file.orderlist%}"/></span>
        </td>
        <td>
            <span class="size">描述：<input type="text" name="attachment_description[{%=file.id%}]" value="{%=file.description%}" /></span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span><?= Yii::t('fileupload', 'Delete') ?></span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span><?= Yii::t('fileupload', 'Cancel') ?></span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}

</script>
<script>
    function inputChecked($obj){
        var td=$obj.parents("tr").siblings('tr').children('td');
        var span=td.children('span');
        if($obj.is(':checked')){
            span.children("input[type='checkbox']").prop('checked',false);
        }
    }
</script>

