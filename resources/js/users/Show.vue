<template>
  <div class="flex flex-col items-center" v-if="status.user === 'success'">
    <p v-if="status.user == 'loading'">.. user loading</p>
    <div v-else-if="user" class="relative mb-8">
      <div class="w-100 h-64 overflow-hidden z-10">
        <UploadableImage
          image-width="1500"
          image-height="300"
          location="cover"
          alt="cover image"
          :user-image="user.data.attributes.cover_image"
          classes="object-cover w-full"
        ></UploadableImage>
      </div>
      <div class="absolute bottom-0 left-0 ml-12 -mb-8 z-20 flex items-center">
        <div class="w-32">
          <UploadableImage
            image-width="800"
            image-height="800"
            location="profile"
            alt="profile image"
            :user-image="user.data.attributes.profile_image"
            classes="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-xl"
          ></UploadableImage>
        </div>
        <p class="ml-4 text-2xl text-gray-100">{{ user.data.attributes.name }}</p>
      </div>
      <div class="absolute bottom-0 right-0 mr-12 mb-4 z-20 flex items-center">
        <button
          v-if="friendButtonText && friendButtonText !== 'Accept'"
          class="py-1 px-3 bg-gray-400 rounded"
          @click="
                        $store.dispatch(
                            'sendFriendRequest',
                            $route.params.userId
                        )
                    "
        >{{ friendButtonText }}</button>
        <button
          v-if="friendButtonText && friendButtonText === 'Accept'"
          class="mr-2 py-1 px-3 bg-blue-500 rounded"
          @click="
                        $store.dispatch(
                            'acceptFriendRequest',
                            $route.params.userId
                        )
                    "
        >Accept</button>
        <button
          v-if="friendButtonText && friendButtonText === 'Accept'"
          class="mr-2 py-1 px-3 bg-gray-400 rounded"
          @click="
                        $store.dispatch(
                            'ignoreFriendRequest',
                            $route.params.userId
                        )
                    "
        >Ignore</button>
      </div>
    </div>

    <div v-if="status.posts === 'loading'">..loading</div>
    <div v-else-if="status.posts === 'success' && posts.length < 1">No posts found.</div>
    <template v-if="posts">
      <Post v-for="post in posts.data" :key="post.data.post_id" :post="post"></Post>
    </template>
  </div>
</template>
<script>
import Post from "../components/Post";
import UploadableImage from "../components/UploadableImage";
import { mapGetters } from "vuex";
export default {
  name: "Show",
  components: {
    Post,
    UploadableImage
  },

  computed: {
    ...mapGetters({
      status: "status",
      user: "user",
      friendButtonText: "friendButtonText",
      posts: "posts"
    })
  },
  mounted() {
    this.$store.dispatch("fetchUser", this.$route.params.userId);
    this.$store.dispatch("fetchUserPosts", this.$route.params.userId);
  }
};
</script>
