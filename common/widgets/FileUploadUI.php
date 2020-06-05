<?php

namespace common\widgets;

use dosamigos\fileupload\FileUploadUI AS FileUploadUIBase;

class FileUploadUI extends FileUploadUIBase
{
    public function registerClientScript()
    {
        $this->clientEvents = $this->initClientEvents();
        parent::registerClientScript();
        $id = $this->options['id'];

        $js = "$('#{$id}').addClass('fileupload-processing');
        var url = $('#{$id}').fileupload('option', 'url');";

        if (!isset($this->clientOptions['noShow'])) {
        $js .= "url += url.indexOf('?') > 0 ? '&action=show' : '?action=show';

        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: url,
            dataType: 'json',
            context: $('#{$id}')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });";
        }

        $this->getView()->registerJs($js);
    }

    protected function initClientEvents()
    {
        $idField = isset($this->fieldOptions['idField']) ? $this->fieldOptions['idField'] : $this->options['id'];
        $singleEvents = [
            'fileuploaddone' => 'function(e, data) {
                var uploadStatus = data.result.files[0];
                if (uploadStatus.status == 200200) {
                    var attachmentId = data.result.files[0].id;
                    $("#' . $idField . '").val(attachmentId);
                } else {
                    alert(uploadStatus.message);
                    $(".template-upload:last-child").remove();
                }
            }',
            'fileuploadfail' => 'function(e, data) {
                var attachmentIdCancel = data.context.find("input[name=attachment_id]").val();
                var attachmentId = $("#' . $idField . '").val();
                if (attachmentIdCancel == attachmentId) {
                    $("#' . $idField . '").val(0);
                }
            }',
            'fileuploadstart' => 'function(e, data) {
            }',
            'fileuploadalways' => 'function(e, data) {
            }',
            'fileuploadstop' => 'function(e, data) {
                $(".template-download:last-child").addClass("lastupload");
                $(".template-download").not(".lastupload").remove();
                $(".template-download:last-child").removeClass("lastupload");
            }',
        ];

        $multipleEvents = [
            'fileuploaddone' => 'function(e, data) {
                var attachmentIds = $("#' . $idField . '").val();
                var attachmentId = data.result.files[0].id;

                $("#' . $idField . '").val(attachmentIds + "," + attachmentId);
            }',
            'fileuploadfail' => 'function(e, data) {
                var attachmentIdsNew = "";
                var attachmentIdCancel = data.context.find("input[name=attachment_id]").val();
                var attachmentIds = $("#' . $idField . '").val();
                attachmentIds = attachmentIds.split(",");
                for (i = 0; i < attachmentIds.length; i++) {
                    if (attachmentIds[i] > 0 && attachmentIds[i] != attachmentIdCancel) {
                        attachmentIdsNew += "," + attachmentIds[i];
                    }
                }

                $("#' . $idField . '").val(attachmentIdsNew);
            }',
            'fileuploadstop' => 'function(e, data) {
            }',
        ];

        if (!empty($this->clientEvents)) {
            return $this->clientEvents;
        }
        if (isset($this->fieldOptions['isSingle']) && $this->fieldOptions['isSingle']) {
            return $singleEvents;
        }
        return $multipleEvents;

    }
}
