1. For Dashboard page summary call this GET api "/dashboard/summary". Here is the response:
{
    "status": true,
    "message": "all summary",
    "data": {
        "userCount": 16,
        "businessCount": 5,
        "productCount": 14,
        "conversationCount": 7,
        "leadCount": 3
    }
}

2. Add Today stats in Dashboard page. Just call this GET API "/dashboard/today/stats" Here is the response:
{
  "message": "today stats",
  "data": [
    "user": 5,
    "business": 1,
    "product": 3,
    "lead": 2,
    "conversation": 18
  ]
}

3. Add weekly graph/charts in Dashboard page. Just call this GET API "/dashboard/weekly/graph" Here is the response:
{
  "labels": ["Sat","Sun","Mon","Tue","Wed","Thu","Fri"],
  "start": "2025-02-15",
  "end": "2025-02-21",

  "user":         [3, 0, 5, 1, 0, 4, 2],
  "business":     [0, 1, 0, 0, 0, 3, 1],
  "product":      [2, 0, 0, 0, 0, 0, 0],
  "lead":         [0, 0, 2, 1, 0, 0, 0],
  "conversation": [10, 8, 6, 0, 4, 0, 0]
}

4. Add Lead-credit-package section. Here is related apis with request and response:
[
  {
    "route": "/lead/credit/packages",
    "method": "GET",
    "request_fields": {
      "is_active": "boolean (Filter active or inactive packages, optional)",
      "per_page": "integer (Pagination limit, default: 10)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "boolean",
      "message": "Lead credit packages fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "name": "string (Package name)",
            "credits": "integer (Total credits)",
            "price": "decimal (Package price)",
            "currency": "string (e.g., BDT)",
            "duration_months": "integer (Validity in months)",
            "is_active": "boolean",
            "created_at": "datetime"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/lead/credit/packages",
    "method": "POST",
    "request_fields": {
      "name": "string (required)",
      "credits": "integer (required, minimum 1)",
      "price": "decimal (required)",
      "currency": "string (optional, default: BDT)",
      "duration_months": "integer (optional, default: 3)",
      "is_active": "boolean (optional, default: true)"
    },
    "response_structure": {
      "status": "boolean",
      "message": "Lead credit package created successfully",
      "data": {
        "id": "integer",
        "name": "string",
        "credits": "integer",
        "price": "decimal",
        "currency": "string",
        "duration_months": "integer",
        "is_active": "boolean",
        "created_at": "datetime"
      }
    },
    "validation_error_response": {
      "status": false,
      "message": "Validation failed",
      "data": {
        "field_name": ["Validation error message"]
      }
    }
  },
  {
    "route": "/lead/credit/packages/{id}",
    "method": "GET",
    "request_fields": {},
    "response_structure": {
      "status": "boolean",
      "message": "Lead credit package details",
      "data": {
        "id": "integer",
        "name": "string",
        "credits": "integer",
        "price": "decimal",
        "currency": "string",
        "duration_months": "integer",
        "is_active": "boolean",
        "created_at": "datetime",
      }
    }
  },
  {
    "route": "/lead/credit/packages/{id}",
    "method": "PUT",
    "request_fields": {
      "name": "string (optional)",
      "credits": "integer (optional)",
      "price": "decimal (optional)",
      "currency": "string (optional)",
      "duration_months": "integer (optional)",
      "is_active": "boolean (optional)"
    },
    "response_structure": {
      "status": "boolean",
      "message": "Lead credit package updated successfully",
      "data": {
        "id": "integer",
        "name": "string",
        "credits": "integer",
        "price": "decimal",
        "currency": "string",
        "duration_months": "integer",
        "is_active": "boolean",
        "created_at": "datetime",
      }
    },
    "validation_error_response": {
      "status": false,
      "message": "Validation failed",
      "data": {
        "field_name": ["Validation error message"]
      }
    }
  },
  {
    "route": "/lead/credit/packages/{id}",
    "method": "DELETE",
    "request_fields": {},
    "response_structure": {
      "status": "boolean",
      "message": "Lead credit package deleted successfully",
      "data": null
    }
  }
]

5. Add Lead-credit-purchases section. Here is related apis with request and response:
[
  {
    "route": "/lead-credit-purchases",
    "method": "GET",
    "request_fields": {
      "business_id": "integer (Filter by business ID, optional)",
      "package_id": "integer (Filter by package ID, optional)",
      "transaction_id": "string (Search by transaction ID, optional)",
      "status": "string (pending, completed, failed)",
      "start_date": "date (Filter by purchase date start)",
      "end_date": "date (Filter by purchase date end)",
      "per_page": "integer (Pagination limit, default: 10)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "boolean",
      "message": "Lead credit purchases fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "business_id": "integer",
            "business_name": "string",
            "package_name": "integer",
            "credits": "integer",
            "amount": "decimal",
            "payment_method": "string or null",
            "transaction_id": "string or null",
            "status": "string (pending, completed, failed)",
            "expire_date": "date or null",
            "created_at": "datetime",
            "updated_at": "datetime"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/lead-credit-purchases/{id}/status",
    "method": "PUT",
    "request_fields": {
      "status": "string (required: pending, completed, failed)"
    },
    "response_structure": {
      "status": "boolean",
      "message": "Purchase status updated successfully",
      "data": {
        "id": "integer",
        "business_id": "integer",
        "package_id": "integer",
        "credits": "integer",
        "amount": "decimal",
        "payment_method": "string or null",
        "transaction_id": "string or null",
        "status": "string",
        "expire_date": "date or null",
        "created_at": "datetime",
        "updated_at": "datetime"
      }
    },
    "validation_error_response": {
      "status": false,
      "message": "Validation failed",
      "data": {
        "status": ["The selected status is invalid."]
      }
    },
    "not_found_response": {
      "status": false,
      "message": "Lead credit purchase not found",
      "data": null
    }
  }
]


6. Now need to create 4 setions/report for Admin:

Business: can see all business, can Edit status('active', 'inactive', 'blocked'), can see all products of a business,
Users: can see all user, can Edit status(active, blocked)
Products: can see all products, can Edit status('active', 'inactive', 'blocked'),
Leads: can see all leads

Here I provided related apis with request and response:

[
  {
    "route": "/users",
    "method": "GET",
    "request_fields": {
      "keyword": "string (Search by name, email, or phone)",
      "start_date": "date (Filter by creation date start)",
      "end_date": "date (Filter by creation date end)",
      "location_id": "integer",
      "status": "string (e.g., 'active', 'blocked')",
      "has_business": "integer (1 or 0 - 1 for users with business, 0 for users without)",
      "per_page": "integer (Pagination limit, default: 15)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "success",
      "message": "users fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "name": "string",
            "profile": "string (URL to profile image, or empty string)",
            "number": "string (user's phone number)",
            "email": "string",
            "address": "string",
            "location": {
              "id": "integer",
              "upazila_name": "string",
              "district_name": "string"
            },
            "post_code": "string",
            "account_verification": "string (e.g., 'verified', 'pending', or null)",
            "business": "object (id, business_name, business_profile) or empty string"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/users/{id}/status",
    "method": "PUT",
    "request_fields": {
      "status": "required|string (New status: 'active' or 'blocked')"
    },
    "response_structure": {
      "status": "success",
      "message": "User status updated successfully",
      "data": "{...User model object...}"
    }
  },
  {
    "route": "/businesses",
    "method": "GET",
    "request_fields": {
      "keyword": "string (Search by name, phone, email, or address)",
      "start_date": "date (Filter by creation date start)",
      "end_date": "date (Filter by creation date end)",
      "location_id": "integer",
      "status": "string (e.g., 'active', 'inactive', 'blocked')",
      "business_type": "array/string (Filter by business type(s))",
      "per_page": "integer (Pagination limit, default: 15)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "success",
      "message": "Businesses fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "slug": "string",
            "business_name": "string",
            "business_profile": "string (URL to profile image, or empty string)",
            "rating": "float (average rating)",
            "business_type": "array",
            "number": "string",
            "alternate_number": "string",
            "in_business": "integer (years established, or null)",
            "location": "string (e.g., 'Upazila, District')",
            "account_verification": "boolean",
            "created_at": "datetime"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/businesses/{id}/status",
    "method": "PUT",
    "request_fields": {
      "status": "required|string (New status: 'active', 'inactive', or 'blocked')"
    },
    "response_structure": {
      "status": "success",
      "message": "Business status updated successfully",
      "data": "{...Business model object...}"
    }
  },
  {
    "route": "/businesses/{id}/products",
    "method": "GET",
    "request_fields": {
      "per_page": "integer (Pagination limit, default: 15)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "success",
      "message": "Business products fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "name": "string",
            "slug": "string",
            "business": "{...BusinessResource object...}",
            "price": "float",
            "moq": "integer (Minimum Order Quantity)",
            "stock": "integer",
            "unit_quantity": "float",
            "product_unit": "{...ProductUnitResource object...}",
            "feature_image": "string (URL to image, or empty string)"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/products",
    "method": "GET",
    "request_fields": {
      "keyword": "string (Search by name, slug, or details)",
      "start_date": "date (Filter by creation date start)",
      "end_date": "date (Filter by creation date end)",
      "status": "string (e.g., 'active', 'inactive', 'blocked')",
      "location_id": "integer (Filters via associated business's location)",
      "category_id": "integer",
      "per_page": "integer (Pagination limit, default: 15)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "success",
      "message": "Products fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "name": "string",
            "slug": "string",
            "business": "{...BusinessResource object...}",
            "price": "float",
            "moq": "integer (Minimum Order Quantity)",
            "stock": "integer",
            "unit_quantity": "float",
            "product_unit": "{...ProductUnitResource object...}",
            "feature_image": "string (URL to image, or empty string)"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/products/{id}/status",
    "method": "PUT",
    "request_fields": {
      "status": "required|string (New status: 'active', 'inactive', or 'blocked')"
    },
    "response_structure": {
      "status": "success",
      "message": "Product status updated",
      "data": "{...Product model object...}"
    }
  },
  {
    "route": "/leads",
    "method": "GET",
    "request_fields": {
      "keyword": "string (Search by description)",
      "start_date": "date (Filter by creation date start)",
      "end_date": "date (Filter by creation date end)",
      "location_id": "integer",
      "category_id": "integer",
      "status": "string (Filter by lead status)",
      "per_page": "integer (Pagination limit, default: 15)",
      "page": "integer (Page number)"
    },
    "response_structure": {
      "status": "success",
      "message": "users fetched successfully",
      "data": {
        "data": [
          {
            "id": "integer",
            "quantity": "float",
            "unit_name": "string",
            "status": "string",
            "description": "string",
            "category": "{...CategoryResource object...}",
            "location": "{...LocationResource object...}",
            "user": {
              "name": "string",
              "number": "string",
              "email": "string (Note: Source code appears to map user number here)",
              "address": "string",
              "account_verification": "object/array (details of verification)"
            },
            "created_at": "datetime",
            "proposal": "integer (count of proposals)"
          }
        ],
        "meta": {
          "current_page": "integer",
          "last_page": "integer",
          "per_page": "integer",
          "total": "integer"
        }
      }
    }
  },
  {
    "route": "/lead/{id}/proposals",
    "method": "GET",
    "request_fields": "None",
    "response_structure": {
      "status": "success",
      "message": "proposals fetched successfully",
      "data": [
        {
          "id": "integer",
          "slug": "string",
          "business_name": "string",
          "business_profile": "string (URL to profile image, or empty string)",
          "rating": "float (average rating)",
          "number": "string (business phone number)",
          "alternate_number": "string",
          "email": "string (business email)",
          "in_business": "integer (years established, or null)",
          "location": "{...Location model object...}",
          "account_verification": "boolean"
        }
      ]
    }
  }
]