<template>
  <div class="bg-white rounded shadow w-2/3 p-4 flex justify-between items-center">
    <div>
      <div>
        <img
          class="w-8 h-8 object-cover rounded-full"
          src="https://scontent.ftbs4-1.fna.fbcdn.net/v/t1.0-9/72350364_2874054232622006_7730376709672796160_n.jpg?_nc_cat=103&_nc_sid=09cbfe&_nc_oc=AQkygazu9iqQXj5FuvWPC8qBL7uRHBqx-F2G_81fw7suIPZND97y63VgFWOctr885EU&_nc_ht=scontent.ftbs4-1.fna&oh=18dbee6b5f487d420d8dc778487000ad&oe=5EE57572"
          alt
        />
      </div>
    </div>
    <div class="flex-1 mx-4">
      <input
        type="text"
        name="body"
        class="w-full h-8 pl-4 bg-gray-200 rounded-full text-sm focus:outline-none focus:shadow-outline"
        placeholder="Add a post"
      />
    </div>

    <div>
      <button class="flex bg-gray-200 rounded-full justify-center items-center w-10 h-10">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="fill-current w-5 h-5">
          <path
            d="M21.8 4H2.2c-.2 0-.3.2-.3.3v15.3c0 .3.1.4.3.4h19.6c.2 0 .3-.1.3-.3V4.3c0-.1-.1-.3-.3-.3zm-1.6 13.4l-4.4-4.6c0-.1-.1-.1-.2 0l-3.1 2.7-3.9-4.8h-.1s-.1 0-.1.1L3.8 17V6h16.4v11.4zm-4.9-6.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6-.9 0-1.6.7-1.6 1.6.1.9.8 1.6 1.6 1.6z"
          />
        </svg>
      </button>
    </div>
  </div>
</template>
<script>
export default {
  name: "NewPost"
};
</script>