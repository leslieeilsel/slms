<template>
  <file-manager style="min-height:600px;" :settings="settings"></file-manager>
</template>
<script>
import "../../styles/bootstrap.4.1.3.min.css";
import "../../styles/fontawesome.v5.7.0.css";

export default {
  data() {
    return {
      settings: {
        // axios headers
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Authorization: `Bearer ${window.localStorage.getItem("user-token")}`
        },
        baseUrl: "http://localhost:5005/file-manager/", // overwrite base url Axios
        windowsConfig: 2, // overwrite config
        lang: "zh_CN" // set language
      }
    };
  },
  watch: {},
  methods: {},
  mounted() {}
};
</script>
<style>
.fm-modal {
  min-height: 600px;
}
</style>