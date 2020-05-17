<template>
  <div>
    <div class="w-100 h-64 overflow-hidden">
      <img class="object-cover with-full" src="https://source.unsplash.com/random/800x600" alt />
    </div>
  </div>
</template>
<script>
export default {
  name: "Show",
  data() {
    return {
      user: {},
      posts: [],
      loading: true
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
      .finally(() => (this.loading = false));

    axios
      .get("api/posts" + this.$route.params.userId)
      .then(res => {
        this.posts = res.data;
      })
      .catch(e => {
        console.error("Unable to fetch posts");
      })
      .finally(() => (this.loading = false));
  }
};
</script>