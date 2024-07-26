<script setup>
import {onBeforeUnmount, onMounted, ref} from 'vue'
import Hls from 'hls.js'

const props = defineProps({
  previewImageLink: {
    type: String,
    default: ''
  },
  link: {
    type: String,
    default: ''
  },
  progress: {
    type: Number,
    default: 0
  },
  title: {
    type: String,
    default: ''
  },
  isMuted: {
    type: Boolean,
    default: false
  },
  isControls: {
    type: Boolean,
    default: true
  },
    onLoaded: {
    type: Function,
    default: () => {}
  },
    onStop: {
    type: Function,
    default: () => {}
  },
})

const manifestLoadAttempts = 60;
const connectionTime = 15000;

const emit = defineEmits(['pause'])
const video = ref(null)
const hls = new Hls()
const interval = ref(null)
const errorCount = ref(0)

onMounted(() => {
    interval.value = setInterval(() => {
        try {
            hls.loadSource(props.link)
        } catch (e) {

        }
    }, 1000)

    prepareVideoPlayer()
})

onBeforeUnmount(() => {
    clearInterval(interval.value);
    interval.value = null;
    props.onLoaded();
    hls.stopLoad();
})


function prepareVideoPlayer() {

    hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
        props.onLoaded();
        if(interval.value) {
            clearInterval(interval.value);
            interval.value = null;
        }

        if (video.value) {
            hls.attachMedia(video.value)
            setTimeout(() => {
                props.onStop()
            }, connectionTime)
            video.value.muted = props.isMuted
            video.value.currentTime = props.progress
        }
    });
    hls.on(Hls.Events.ERROR, function (event, data) {
        if (errorCount.value > manifestLoadAttempts) {
            clearInterval(interval.value);
            interval.value = null;
            props.onLoaded();
            props.onError('Timeout Error');
        } else {
            errorCount.value++
        }
    });


}

function pause() {
  const currentTime = video?.value?.currentTime || 0

  emit('pause', currentTime)
}
</script>

<template>
  <video
      @pause="pause"
      @ended="pause"
      ref="video"
      :poster="previewImageLink"
      :controls="isControls"
      :title="title"
      width="650"
  >
    <source
        :src="link"
        type="application/x-mpegURL"
    />
  </video>
</template>
