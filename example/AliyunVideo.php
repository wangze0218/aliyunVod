<?php
namespace App\Services;

use James\AliyunVod\VodUploadOss;
use OSS\Core\OssException;

Class AliyunVideo{
    private $accessKeyId = '<AccessKeyId>';                    // 您的AccessKeyId
    private $accessKeySecret = '<AccessKeySecret>';            // 您的AccessKeySecret
    private $localFile = '/Users/yours/Video/testVideo.flv';   // 需要上传到VOD的本地视频文件的完整路径


    public function __construct($accessKeyId,$accessKeySecret,$localFile)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->localFile = $localFile;
    }

    public function boot()
    {
        $upload = new VodUploadOss();
        try {
            // 初始化VOD客户端并获取上传地址和凭证
            $vodClient = $upload->init_vod_client($accessKeyId, $accessKeySecret);
            $createRes = $upload->create_upload_video($vodClient);

            // 执行成功会返回VideoId、UploadAddress和UploadAuth
            $videoId = $createRes->VideoId;
            $uploadAddress = json_decode(base64_decode($createRes->UploadAddress), true);
            $uploadAuth = json_decode(base64_decode($createRes->UploadAuth), true);

            // 使用UploadAuth和UploadAddress初始化OSS客户端
            $ossClient = $upload->init_oss_client($uploadAuth, $uploadAddress);

            // 上传文件，注意是同步上传会阻塞等待，耗时与文件大小和网络上行带宽有关
            //$result = upload_local_file($ossClient, $uploadAddress, $localFile);
            $result = $upload->multipart_upload_file($ossClient, $uploadAddress, $localFile);
            printf("Succeed, VideoId: %s", $videoId);
            return $result;

        } catch (OssException $e) {
            // var_dump($e);
            printf("Failed, ErrorMessage: %s", $e->getMessage());
        }
    }

}