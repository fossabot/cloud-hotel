<template>
  <v-layout column>
    <v-flex>
      <RoomList/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoomList from "~/components/RoomList"
  export default {
    name: "Rooms",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    components: {RoomList},
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('room_list')) {
          vm.$router.push('/400')
        }
      })
    }
  }
</script>

<style scoped>

</style>
