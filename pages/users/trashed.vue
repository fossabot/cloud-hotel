<template>
  <v-layout column>
    <v-flex>
      <UserList trashed/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import UserList from "~/components/UserList"
  export default {
    name: "Users",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    components: {UserList},
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('user_listTrashed')) {
          vm.$router.push('/400')
        }
      })
    }
  }
</script>

<style scoped>

</style>
