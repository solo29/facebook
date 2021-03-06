<template>
  <div class="bg-white rounded shadow w-2/3 p-4">
    <div class="flex justify-between items-center">
      <div>
        <div>
          <img class="w-8 h-8 object-cover rounded-full" :src="profileImage" alt />
        </div>
      </div>
      <div class="flex-1 mx-4 flex">
        <input
          v-model="postMessage"
          type="text"
          name="body"
          class="w-full h-8 pl-4 bg-gray-200 rounded-full text-sm focus:outline-none focus:shadow-outline"
          placeholder="Add a post"
        />
        <transition name="fade">
          <button
            v-if="postMessage"
            @click="postHandler"
            class="bg-gray-200 ml-2 px-3 py-1 rounded-full"
          >Post</button>
        </transition>
      </div>

      <div>
        <button
          ref="postImage"
          class="dz-clickable flex bg-gray-200 rounded-full justify-center items-center w-10 h-10"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="dz-clickable fill-current w-5 h-5"
          >
            <path
              d="M21.8 4H2.2c-.2 0-.3.2-.3.3v15.3c0 .3.1.4.3.4h19.6c.2 0 .3-.1.3-.3V4.3c0-.1-.1-.3-.3-.3zm-1.6 13.4l-4.4-4.6c0-.1-.1-.1-.2 0l-3.1 2.7-3.9-4.8h-.1s-.1 0-.1.1L3.8 17V6h16.4v11.4zm-4.9-6.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6-.9 0-1.6.7-1.6 1.6.1.9.8 1.6 1.6 1.6z"
            />
          </svg>
        </button>
      </div>
    </div>
    <div class="dropzone-previews">
      <div id="dz-template" class="hidden">
        <div class="dz-preview dz-file-preview mt-4">
          <div class="dz-details">
            <img data-dz-thumbnail class="w-32 h-32" />
            <button data-dz-remove class="text-xs">Remove</button>
          </div>
          <div class="dz-progress">
            <span class="dz-upload" data-dz-upload></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import _ from "lodash";
import { mapGetters } from "vuex";
import Dropzone from "dropzone";
export default {
  name: "NewPost",
  computed: {
    postMessage: {
      get() {
        return this.$store.getters.postMessage;
      },
      set: _.debounce(function(postMessage) {
        this.$store.commit("updateMessage", postMessage);
      }, 300)
    },
    ...mapGetters({
      profileImage: "profileImage"
    }),
    settings() {
      return {
        paramName: "image",
        url: "/api/posts",
        acceptedFiles: "image/*",
        params: {
          width: 1000,
          height: 1000
        },
        maxFiles: 1,
        autoProcessQueue: false,
        previewsContainer: ".dropzone-previews",
        previewTemplate: document.querySelector("#dz-template").innerHTML,
        clickable: ".dz-clickable",
        headers: {
          "X-CSRF-TOKEN": document.head.querySelector("meta[name=csrf-token]").content
        },
        sending: (file, xhr, formData) => {
          formData.append("body", this.$store.getters.postMessage);
        },
        success: (e, res) => {
          console.log(e, res.data);
          this.$store.commit("pushPost", res);
          this.dropzone.removeAllFiles();
          console.log("uploaded");
        },
        maxfilesexceeded: file => {
          this.dropzone.removeAllFiles();
          this.dropzone.addFile(file);
        }
      };
    }
  },
  data() {
    return {
      dropzone: null
    };
  },
  mounted() {
    this.dropzone = new Dropzone(this.$refs.postImage, this.settings);
  },
  methods: {
    postHandler() {
      if (this.dropzone.getAcceptedFiles().length) {
        console.log("have files");
        this.dropzone.processQueue();
      } else {
        console.log("dont have files");
        this.$store.dispatch("postMessage");
      }
      this.$store.commit("updateMessage", "");
    }
  }
};
</script>
<style lang="scss" scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
