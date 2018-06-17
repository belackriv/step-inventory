import { Model } from '@vuex-orm/core';
import Organization from './organization.js';

export default class Myself extends Model {
  static entity = 'myself'

  static fields () {
    return {
      id: this.number(null),
      isAccountOwner: this.boolean(null),
      username: this.string(null),
      email: this.string(null),
      firstName: this.string(null),
      lastName: this.string(null),
      isActive: this.boolean(null),
      receivesInventoryAlert: this.boolean(null),
      organization_id: this.attr(null),
      organization: this.belongsTo(Organization, 'organization_id'),
      menuItems: this.attr(null)
      /*
      defaultDepartment: {},
      userRoles: [],
      currentDepartment: {},
      inventoryAlertLogs: [],
      roleHierarchy: {}
      */
    };
  }
};

/*
{
  "isAccountOwner": false,
  "id": 1,
  "username": "belac",
  "email": "belackriv@gmail.com",
  "firstName": "Belac",
  "lastName": "Kriv",
  "isActive": true,
  "receivesInventoryAlert": true,
  "organization": {
    "id": 7,
    "account": {
      "ownerSelections": [
        {
          "isAccountOwner": true,
          "id": 9,
          "username": "test1",
          "email": "test1@test.com",
          "firstName": "test",
          "lastName": "one",
          "isActive": true,
          "receivesInventoryAlert": false,
          "userRoles": [
            {
              "id": 5,
              "role": {
                "id": 3,
                "name": "Admin",
                "role": "ROLE_ADMIN",
                "isAllowedToSwitch": false
              }
            }
          ]
        },
        {
          "isAccountOwner": false,
          "id": 10,
          "username": "rwar",
          "email": "ads@ss.com",
          "firstName": "rwar",
          "lastName": "awr",
          "isActive": true,
          "receivesInventoryAlert": false,
          "userRoles": [
            {
              "id": 6,
              "role": {
                "id": 2,
                "name": "Lead",
                "role": "ROLE_LEAD",
                "isAllowedToSwitch": false
              }
            },
            {
              "id": 7,
              "role": {
                "id": 3,
                "name": "Admin",
                "role": "ROLE_ADMIN",
                "isAllowedToSwitch": false
              }
            }
          ]
        }
      ],
      "id": 1,
      "owner": {
        "isAccountOwner": true,
        "id": 9,
        "username": "test1",
        "email": "test1@test.com",
        "firstName": "test",
        "lastName": "one",
        "isActive": true,
        "receivesInventoryAlert": false,
        "userRoles": [
          {
            "id": 5,
            "role": {
              "id": 3,
              "name": "Admin",
              "role": "ROLE_ADMIN",
              "isAllowedToSwitch": false
            }
          }
        ]
      },
      "subscription": {
        "stripeStatuses": {
          "trialing": 1,
          "active": 2,
          "past_due": 3,
          "canceled": 4,
          "unpaid": 5,
          "legacy": 6
        },
        "id": 11,
        "plan": {
          "id": 7,
          "name": "Step Pro",
          "description": "12 Concurrent Users, 10,000 Monthly TIDs, 2c per TID After",
          "amount": 7495,
          "currency": "usd",
          "interval": "month",
          "intervalCount": 1,
          "trialPeriodDays": 10,
          "maxConcurrentUsers": 12,
          "maxMonthlyTravelerIds": 10000,
          "travelerIdOverageCharge": 0,
          "isActive": true
        },
        "createdAt": "2017-09-30T14:07:49.000000+00:00",
        "cancelAtPeriodEnd": false,
        "currentPeriodEnd": "2017-10-10T14:07:49.000000+00:00",
        "currentPeriodStart": "2017-09-30T14:07:49.000000+00:00",
        "quantity": 1,
        "startAt": "2017-10-01T18:32:11.000000+00:00",
        "status": 1,
        "taxPercent": 0,
        "trialEnd": "2017-10-10T14:07:49.000000+00:00",
        "trialStart": "2017-09-30T14:07:49.000000+00:00"
      },
      "accountChanges": [
        {
          "id": 1,
          "changedBy": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "changedAt": "2016-12-18T22:35:40.000000+00:00",
          "newOwner": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "discriminator": "AccountOwnerChange"
        },
        {
          "id": 6,
          "changedBy": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "changedAt": "2016-12-19T02:31:07.000000+00:00",
          "oldOwner": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "newOwner": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "discriminator": "AccountOwnerChange"
        },
        {
          "id": 7,
          "changedBy": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "changedAt": "2016-12-20T12:29:21.000000+00:00",
          "oldPlan": {
            "id": 2,
            "name": "Test Two",
            "description": "Test 2",
            "amount": 5000,
            "currency": "usd",
            "interval": "month",
            "intervalCount": 1,
            "trialPeriodDays": 10,
            "maxConcurrentUsers": 1,
            "maxMonthlyTravelerIds": 10,
            "travelerIdOverageCharge": 0,
            "isActive": false
          },
          "newPlan": {
            "id": 1,
            "name": "Test Three",
            "description": "Test Three Plan",
            "amount": 6000,
            "currency": "usd",
            "interval": "month",
            "intervalCount": 1,
            "trialPeriodDays": 10,
            "maxConcurrentUsers": 3,
            "maxMonthlyTravelerIds": 30,
            "travelerIdOverageCharge": 0,
            "isActive": false
          },
          "discriminator": "AccountPlanChange"
        },
        {
          "id": 8,
          "changedBy": {
            "isAccountOwner": true,
            "id": 9,
            "username": "test1",
            "email": "test1@test.com",
            "firstName": "test",
            "lastName": "one",
            "isActive": true,
            "receivesInventoryAlert": false,
            "userRoles": [
              {
                "id": 5,
                "role": {
                  "id": 3,
                  "name": "Admin",
                  "role": "ROLE_ADMIN",
                  "isAllowedToSwitch": false
                }
              }
            ]
          },
          "changedAt": "2016-12-20T12:39:02.000000+00:00",
          "oldPlan": {
            "id": 1,
            "name": "Test Three",
            "description": "Test Three Plan",
            "amount": 6000,
            "currency": "usd",
            "interval": "month",
            "intervalCount": 1,
            "trialPeriodDays": 10,
            "maxConcurrentUsers": 3,
            "maxMonthlyTravelerIds": 30,
            "travelerIdOverageCharge": 0,
            "isActive": false
          },
          "newPlan": {
            "id": 2,
            "name": "Test Two",
            "description": "Test 2",
            "amount": 5000,
            "currency": "usd",
            "interval": "month",
            "intervalCount": 1,
            "trialPeriodDays": 10,
            "maxConcurrentUsers": 1,
            "maxMonthlyTravelerIds": 10,
            "travelerIdOverageCharge": 0,
            "isActive": false
          },
          "discriminator": "AccountPlanChange"
        }
      ],
      "bills": [
        {
          "id": 9,
          "chargedAt": "2016-12-21T12:37:25.000000+00:00",
          "amount": 12.28,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 10,
          "chargedAt": "2016-12-20T12:29:20.000000+00:00",
          "amount": 0,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 11,
          "chargedAt": "2016-12-19T02:32:00.000000+00:00",
          "amount": 50,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 12,
          "chargedAt": "2016-12-19T02:29:44.000000+00:00",
          "amount": 60,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 13,
          "chargedAt": "2016-12-19T02:25:17.000000+00:00",
          "amount": 50,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 14,
          "chargedAt": "2016-12-19T02:22:49.000000+00:00",
          "amount": 50,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 15,
          "chargedAt": "2016-12-18T23:57:16.000000+00:00",
          "amount": 0,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 16,
          "chargedAt": "2016-12-18T22:38:06.000000+00:00",
          "amount": 0,
          "isClosed": true,
          "currency": "usd"
        },
        {
          "id": 41,
          "chargedAt": "2017-09-30T14:07:49.000000+00:00",
          "amount": 0,
          "isClosed": true,
          "currency": "usd"
        }
      ],
      "paymentSources": [
        {
          "id": 2,
          "externalId": "card_19TsIZJIK2uPDjzOzl3xNAty",
          "brand": "MasterCard",
          "last4": "4444",
          "expirationMonth": "7",
          "expirationYear": "2017",
          "discriminator": "PaymentCardSource"
        },
        {
          "id": 3,
          "externalId": "card_19TsOtJIK2uPDjzOVG03Zla4",
          "brand": "American Express",
          "last4": "0005",
          "expirationMonth": "6",
          "expirationYear": "2017",
          "discriminator": "PaymentCardSource"
        },
        {
          "id": 7,
          "externalId": "src_1B7leGFM0zYTkalPrFrPpE99",
          "brand": "Visa",
          "last4": "4242",
          "expirationMonth": "9",
          "expirationYear": "2020",
          "discriminator": "PaymentCardSource"
        }
      ]
    },
    "name": "Test2 Org One"
  },
  "defaultDepartment": {
    "id": 1,
    "name": "Home"
  },
  "userRoles": [
    {
      "id": 1,
      "role": {
        "id": 4,
        "name": "Dev",
        "role": "ROLE_DEV",
        "isAllowedToSwitch": true
      }
    }
  ],
  "currentDepartment": {
    "id": 1,
    "name": "Home"
  },
  "inventoryAlertLogs": [
    {
      "id": 3,
      "inventoryAlert": {
        "types": {
          "1": "Less Than",
          "2": "Greater Than"
        },
        "id": 2,
        "department": {
          "id": 11,
          "name": "Default Department"
        },
        "sku": {
          "id": 1,
          "name": "SkuOne",
          "number": 1,
          "label": "00001",
          "supplierCode": "",
          "supplierSku": "",
          "part": {
            "id": 1,
            "name": "PartOne",
            "partId": "one",
            "partAltId": "1",
            "description": "p1",
            "isActive": true
          },
          "isVoid": false,
          "quantity": 1
        },
        "isActive": true,
        "count": 10000,
        "type": 1
      },
      "performedAt": "2017-10-07T15:33:54.000000+00:00",
      "count": 0,
      "isActive": true
    }
  ],
  "roleHierarchy": {
    "ROLE_LEAD": [
      "ROLE_USER"
    ],
    "ROLE_ADMIN": [
      "ROLE_LEAD"
    ],
    "ROLE_DEV": [
      "ROLE_ADMIN"
    ]
  }
}

*/
