<template>
  <div class="flex flex-col items-center py-4">
    <NewPost></NewPost>

    <p v-if="newsStatus=='loading'">..loading</p>
    <Post
      v-else-if="newsStatus=='success'&& posts.data"
      v-for="post in posts.data"
      :key="post.data.post_id"
      :post="post"
    ></Post>
  </div>
</template>
<script>
import NewPost from "../components/NewPost";
import Post from "../components/Post";
import { mapGetters } from "vuex";
export default {
  name: "NewsFeed",
  components: {
    NewPost,
    Post
  },
  computed: {
    ...mapGetters({
      posts: "posts",
      newsStatus: "newsStatus"
    })
  },
  mounted() {
    this.$store.dispatch("fetchNewsPosts");
  }
};
</script>