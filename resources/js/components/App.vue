<template>
  <div class="flex flex-col flex-1 h-screen overflow-y-hidden" v-if="authUser">
    <Nav></Nav>
    <div class="overflow-y-hidden flex-1 flex">
      <SideBar></SideBar>
      <div class="overflow-x-hidden w-2/3">
        <router-view :key="$route.fullPath"></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import Nav from "./Nav";
import SideBar from "./SideBar";
import { mapGetters } from "vuex";
export default {
  name: "App",
  components: {
    Nav,
    SideBar
  },
  computed: {
    ...mapGetters(["authUser"])
  },
  mounted() {
    this.$store.dispatch("fetchAuthUser");
    this.$store.dispatch("changePageTitle", this.$route.meta.title);
  },
  watch: {
    $route(to, from) {
      this.$store.dispatch("changePageTitle", to.meta.title);
    }
  }
};
</script>
