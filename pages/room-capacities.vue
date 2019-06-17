<template>
  <v-layout column>
    <v-flex>
      <RoomCapacityList/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoomCapacityList from "~/components/RoomCapacityList"
  export default {
    name: "RoomCapacities",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    components: {RoomCapacityList},
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('roomCapacity_list')) {
          vm.$router.push('/400')
        }
      })
    }
  }
</script>

<style scoped>

</style>
