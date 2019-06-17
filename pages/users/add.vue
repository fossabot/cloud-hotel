<template>
  <v-layout column>
    <v-flex>
      <UserAddForm/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import UserAddForm from "~/components/UserAddForm"

  export default {
    name: "UserAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('user_create')) {
          vm.$router.push('/400')
        }
      })
    },
    components: {UserAddForm},
  }
</script>

<style scoped>

</style>
