import { mainRouter, constantRouterMap } from '@/router/router';

/**
 * 通过meta.role判断是否与当前用户权限匹配
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.indexOf(role) >= 0);
  } else {
    return true;
  }
}

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param roles
 */
function filterAsyncRouter(asyncRouterMap, roles) {
  const accessedRouters = asyncRouterMap.filter(route => {
    if (hasPermission(roles, route)) {
      if (route.children && route.children.length) {
        route.children = filterAsyncRouter(route.children, roles);
      }
      return true;
    }
    return false;
  });
  return accessedRouters;
}

const route = {
  state: {
    routers: constantRouterMap,
    addRouters: []
  },
  mutations: {
    setRoutes: (state, routers) => {
      state.addRouters = routers;
      state.routers = constantRouterMap.concat(routers);
    }
  },
  actions: {
    async generateRoutes({commit}, data) {
      const { roles } = data;
      let accessedRouters;
      if (roles.includes('admin')) {
        accessedRouters = mainRouter;
      } else {
        accessedRouters = filterAsyncRouter(mainRouter, roles);
      }
      commit('setRoutes', accessedRouters)
    }
  }
}

export default route
