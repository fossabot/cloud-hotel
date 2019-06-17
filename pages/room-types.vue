<template>
  <v-layout column>
    <v-flex>
      <RoomTypeList/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoomTypeList from "~/components/RoomTypeList"
  export default {
    name: "RoomTypes",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    components: {RoomTypeList},
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('roomType_list')) {
          vm.$router.push('/400')
        }
      })
    }
  }
</script>

<style scoped>

</style>
