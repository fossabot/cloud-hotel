<template>
  <v-layout column>
    <v-flex>
      <HotelAddForm/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import HotelAddForm from "~/components/HotelAddForm"

  export default {
    name: "HotelAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('hotel_create')) {
          vm.$router.push('/400')
        }
      })
    },
    components: {HotelAddForm},
  }
</script>

<style scoped>

</style>
