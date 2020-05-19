<template>
  <div class="flex flex-col items-center">
    <p v-if="userStatus=='loading'">.. user loading</p>
    <div v-else-if="user" class="relative mb-8">
      <div class="w-100 h-64 overflow-hidden z-10">
        <img
          class="object-cover with-full"
          src="https://images.unsplash.com/photo-1589050590928-b7a42c4e3f12?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=685&ixlib=rb-1.2.1&q=80&w=800"
          alt
        />
      </div>
      <div class="absolute bottom-0 left-0 ml-12 -mb-8 z-20 flex items-center">
        <div class="w-32">
          <img
            class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-xl"
            src="https://scontent.ftbs4-1.fna.fbcdn.net/v/t1.0-9/72350364_2874054232622006_7730376709672796160_n.jpg?_nc_cat=103&_nc_sid=09cbfe&_nc_oc=AQkygazu9iqQXj5FuvWPC8qBL7uRHBqx-F2G_81fw7suIPZND97y63VgFWOctr885EU&_nc_ht=scontent.ftbs4-1.fna&oh=18dbee6b5f487d420d8dc778487000ad&oe=5EE57572"
            alt
          />
        </div>
        <p class="ml-4 text-2xl text-gray-100">{{user.data.attributes.name}}</p>
      </div>
      <div class="absolute bottom-0 right-0 mr-12 mb-4 z-20 flex items-center">
        <button
          v-if="friendButtonText && friendButtonText!=='Accept'"
          class="py-1 px-3 bg-gray-400 rounded"
          @click="$store.dispatch('sendFriendRequest',$route.params.userId)"
        >{{friendButtonText}}</button>
        <button
          v-if="friendButtonText && friendButtonText==='Accept'"
          class="mr-2 py-1 px-3 bg-blue-500 rounded"
          @click="$store.dispatch('acceptFriendRequest',$route.params.userId)"
        >Accept</button>
        <button
          v-if="friendButtonText && friendButtonText==='Accept'"
          class="mr-2 py-1 px-3 bg-gray-400 rounded"
          @click="$store.dispatch('ignoreFriendRequest',$route.params.userId)"
        >Ignore</button>
      </div>
    </div>
    <p v-if="postLoading">..loading</p>
    <Post v-else v-for="post in posts.data" :key="post.data.post_id" :post="post"></Post>

    <p v-if="!postLoading&&!posts.data">No posts found.</p>
  </div>
</template>
<script>
import Post from "../components/Post";
import { mapGetters } from "vuex";
export default {
  name: "Show",
  components: {
    Post
  },
  data() {
    return {
      posts: [],
      postLoading: true
    };
  },
  computed: {
    ...mapGetters({
      user: "user",
      userStatus: "userStatus",
      friendButtonText: "friendButtonText"
    })
  },
  mounted() {
    this.$store.dispatch("fetchUser", this.$route.params.userId);

    axios
      .get("/api/users/" + this.$route.params.userId + "/posts")
      .then(res => {
        this.posts = res.data;
      })
      .catch(e => {
        console.error("Unable to fetch posts");
      })
      .finally(() => (this.postLoading = false));
  }
};
</script>