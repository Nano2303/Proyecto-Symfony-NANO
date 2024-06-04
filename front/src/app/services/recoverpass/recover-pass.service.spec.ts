import { TestBed } from '@angular/core/testing';

import { RecoverPassService } from './recover-pass.service';

describe('RecoverPassService', () => {
  let service: RecoverPassService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(RecoverPassService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
