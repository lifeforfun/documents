##### 文件大小计算
> video_size = video_bitrate * time_in_seconds / 8
>
> 计算未压缩视频:<br>
> audio_size = sampling_rate * bit_depth * channels * time_in_seconds / 8
> <br>
> 计算压缩视频:<br>
> audio_size = bitrate * time_in_seconds / 8

##### 视频处理过程
- 转码
> flv[demux] -> h264/aac[decode] -> yuv/pcm[raw data] -> h264/aac[encode] -> mp4[mux]

- 摄像头采集
> yuv/pcm[raw data] -> h264/aac[encode] -> flv[mux]

- 播放器
> flv[demux] -> h264/aac[decode] -> yuv/pcm[raw data]

> mux/demux 对应ffmpeg中的AVStream, codec/decode对应AVCodec, raw data对应AVFrame