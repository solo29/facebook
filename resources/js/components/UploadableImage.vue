<template>
    <div>
        <img
            class="object-cover with-full"
            :class="classes"
            :src="imageObject.data.attributes.path"
            :alt="alt"
            ref="userImage"
        />
    </div>
</template>
<script>
import DropZone from "dropzone";
import { mapGetters } from "vuex";
export default {
    name: "UploadableImage",
    props: [
        "imageWidth",
        "imageHeight",
        "location",
        "userImage",
        "classes",
        "alt"
    ],
    data() {
        return {
            dropZone: null,
            uploadedImage: null
        };
    },
    mounted() {
        if (this.authUser.data.user_id == this.$route.params.userId)
            this.dropZone = new DropZone(this.$refs.userImage, this.settings);
    },
    computed: {
        ...mapGetters(["authUser"]),
        settings() {
            return {
                paramName: "image",
                url: "/api/user-images",
                acceptedFiles: "image/*",
                params: {
                    width: this.imageWidth,
                    height: this.imageHeight,
                    location: this.location
                },
                headers: {
                    "X-CSRF-TOKEN": document.head.querySelector(
                        "meta[name=csrf-token]"
                    ).content
                },
                success: (e, res) => {
                    console.log(e, res);
                    console.log("uploaded");
                    this.uploadedImage = res;

                    this.$store.dispatch("fetchAuthUser");
                    this.$store.dispatch(
                        "fetchUser",
                        this.$route.params.userId
                    );
                    this.$store.dispatch(
                        "fetchUserPosts",
                        this.$route.params.userId
                    );
                }
            };
        },
        imageObject() {
            return this.uploadedImage || this.userImage;
        }
    }
};
</script>
