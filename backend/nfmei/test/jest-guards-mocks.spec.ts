jest.mock('src/common/guards/jwt-auth.guard', () => ({
  JwtAuthGuard: class {
    canActivate() { return true; }
  },
}));

jest.mock('src/common/guards/local-auth.guards', () => ({
  LocalAuthGuard: class {
    canActivate() { return true; }
  },
}));

jest.spyOn(global.console, 'error').mockImplementation(() => {});