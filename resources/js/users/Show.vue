<template>
  <div class="flex flex-col items-center">
    <p v-if="userLoading">.. user loading</p>
    <div v-else class="relative mb-8">
      <div class="w-100 h-64 overflow-hidden z-10">
        <img class="object-cover with-full" src="https://source.unsplash.com/random/800x600" alt />
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
    </div>
    <p v-if="postLoading">..loading</p>
    <Post v-else v-for="post in posts.data" :key="post.data.post_id" :post="post"></Post>

    <p v-if="!postLoading&&!posts.data">No posts found.</p>
  </div>
</template>
<script>
import Post from "../components/Post";
export default {
  name: "Show",
  components: {
    Post
  },
  data() {
    return {
      user: {},
      posts: [],
      userLoading: true,
      postLoading: true
    };
  },
  mounted() {
    axios
      .get("/api/users/" + this.$route.params.userId)
      .then(response => {
        this.user = response.data;
      })
      .catch(e => {
        console.error(e);
      })
      .finally(() => (this.userLoading = false));

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