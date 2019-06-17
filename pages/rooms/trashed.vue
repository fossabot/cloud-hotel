<template>
  <v-layout column>
    <v-flex>
      <HotelList trashed/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import HotelList from "~/components/HotelList"
  export default {
    name: "Hotels",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    components: {HotelList},
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('room_listTrashed')) {
          vm.$router.push('/400')
        }
      })
    }
  }
</script>

<style scoped>

</style>
