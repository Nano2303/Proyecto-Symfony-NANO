import { TestBed } from '@angular/core/testing';

import { OrdenesServiceService } from './ordenes-service.service';

describe('OrdenesServiceService', () => {
  let service: OrdenesServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(OrdenesServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
