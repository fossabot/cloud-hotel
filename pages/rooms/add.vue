<template>
  <v-layout column>
    <v-flex>
      <RoomAddForm/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoomAddForm from "~/components/RoomAddForm"

  export default {
    name: "RoomAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('room_create')) {
          vm.$router.push('/400')
        }
      })
    },
    components: {RoomAddForm},
  }
</script>

<style scoped>

</style>
