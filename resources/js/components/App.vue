<template>
  <div class="flex flex-col flex-1 h-screen overflow-y-hidden">
    <Nav></Nav>
    <div class="overflow-y-hidden flex-1 flex">
      <SideBar></SideBar>
      <div class="overflow-x-hidden w-2/3">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import Nav from "./Nav";
import SideBar from "./SideBar";
export default {
  name: "App",
  components: {
    Nav,
    SideBar
  },
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
