# Cardmarket API 2.0 - Wants List Management

Requests grouped to wants list management let you retrieve information about your stored wants lists, as well as let you manipulate them - creating new lists, add, edit, and delete wants lists and wants.

## Endpoints Overview

| Resource | Allowed HTTP Methods | Ded | Wid | 3rd | Description |
|----------|---------------------|-----|-----|-----|-------------|
| **Wants Lists** | GET | ✓ | - | ✓ | Returns all Wants List entities for the authenticated user |
| | POST | ✓ | - | ✓ | Creates a new wants list for the authenticated user |
| **Wants Lists Items** | GET | ✓ | - | ✓ | Returns all Wants List Item entities for a specified wants list |
| | PUT | ✓ | - | ✓ | Either change the name of the specified wantslist or manage items on it, i.e. add, edit, and delete items |
| | DELETE | ✓ | - | ✓ | Deletes the specified wants list and all items on it |

## Wants Lists

### Get All Wants Lists

**Endpoint:** `GET /wantslist`

**Availability:** Dedicated, 3rd Party

**Description:** Returns all wants lists for the authenticated user.

**Response entity:** Array of WantsList

**Example:**
```
GET /wantslist
```

### Create Wants List

**Endpoint:** `POST /wantslist`

**Availability:** Dedicated, 3rd Party

**Description:** Creates a new wants list for the authenticated user.

**Request parameters (XML body):**
```xml
<request>
  <wantslist>
    <name>My New Wants List</name>
    <idGame>1</idGame>
  </wantslist>
</request>
```

**Required fields:**
- `name` (string) - Name of the wants list
- `idGame` (int) - ID of the game

**Response entity:** WantsList (newly created wants list)

## Wants Lists Items

### Get Wants List Items

**Endpoint:** `GET /wantslist/{idWantslist}`

**Availability:** Dedicated, 3rd Party

**Description:** Returns all items for a specified wants list.

**Path parameters:**
- `idWantslist` (int) - ID of the wants list

**Response entity:** WantsList (with items array)

**Example:**
```
GET /wantslist/12345
```

### Update Wants List (Rename or Manage Items)

**Endpoint:** `PUT /wantslist/{idWantslist}`

**Availability:** Dedicated, 3rd Party

**Description:** Either changes the name of the specified wants list or manages items on it (add, edit, delete items).

**Path parameters:**
- `idWantslist` (int) - ID of the wants list

#### Rename Wants List

**Request parameters (XML body):**
```xml
<request>
  <wantslist>
    <name>New Wants List Name</name>
  </wantslist>
</request>
```

**Response entity:** WantsList

#### Add Items to Wants List

**Request parameters (XML body):**
```xml
<request>
  <action>addItem</action>
  <item>
    <idProduct>273799</idProduct>
    <minCondition>NM</minCondition>
    <wishPrice>10.00</wishPrice>
    <count>4</count>
    <isFoil>false</isFoil>
    <isSigned>false</isSigned>
    <isAltered>false</isAltered>
  </item>
  <item>
    <!-- additional item -->
  </item>
</request>
```

**Required fields for item:**
- `idProduct` (int) or `idMetaproduct` (int) - Product or metaproduct ID
- `minCondition` (string) - Minimum condition (MT, NM, EX, GD, LP, PL, PO)

**Optional fields:**
- `wishPrice` (float) - Desired maximum price
- `count` (int) - Number of copies wanted (default: 1)
- `isFoil` (boolean) - Want foil version? (default: false)
- `isSigned` (boolean) - Want signed version? (default: false)
- `isAltered` (boolean) - Want altered version? (default: false)
- `idLanguage` (int) - Preferred language ID

#### Edit Items in Wants List

**Request parameters (XML body):**
```xml
<request>
  <action>editItem</action>
  <item>
    <idWant>987654</idWant>
    <minCondition>EX</minCondition>
    <wishPrice>8.00</wishPrice>
    <count>2</count>
  </item>
</request>
```

**Required fields:**
- `idWant` (int) - ID of the wants list item to edit

**Editable fields:**
- `minCondition` (string)
- `wishPrice` (float)
- `count` (int)
- `isFoil` (boolean)
- `isSigned` (boolean)
- `isAltered` (boolean)
- `idLanguage` (int)

#### Delete Items from Wants List

**Request parameters (XML body):**
```xml
<request>
  <action>deleteItem</action>
  <item>
    <idWant>987654</idWant>
  </item>
  <item>
    <idWant>876543</idWant>
  </item>
</request>
```

**Required fields:**
- `idWant` (int) - ID of the wants list item to delete

**Response entity:** WantsList (updated wants list)

### Delete Wants List

**Endpoint:** `DELETE /wantslist/{idWantslist}`

**Availability:** Dedicated, 3rd Party

**Description:** Deletes the specified wants list and all items on it.

**Path parameters:**
- `idWantslist` (int) - ID of the wants list

**Response:** 200 OK (no body)

**Example:**
```
DELETE /wantslist/12345
```

## Condition Values

- **MT** - Mint
- **NM** - Near Mint
- **EX** - Excellent
- **GD** - Good
- **LP** - Light Played
- **PL** - Played
- **PO** - Poor

## Actions

When using PUT to manage items, specify the action:

- **`addItem`** - Add new items to the wants list
- **`editItem`** - Edit existing items in the wants list
- **`deleteItem`** - Delete items from the wants list

If no action is specified, the request will rename the wants list.

## Product vs Metaproduct

When adding items to a wants list, you can specify either:

- **`idProduct`** - Specific product (e.g., "Black Lotus" from Alpha)
- **`idMetaproduct`** - Metaproduct (e.g., "Black Lotus" from any set)

Using metaproduct allows matching articles across all printings of a card.

## Error States

**400 Bad Request:**
- Invalid condition value
- Missing required fields
- Invalid price or count values
- Both idProduct and idMetaproduct specified

**404 Not Found:**
- Wants list with given ID doesn't exist
- Wants list doesn't belong to authenticated user
- Item with given idWant doesn't exist

**409 Conflict:**
- Wants list name already exists for this user
- Duplicate item already in wants list

## Notes

- All endpoints require OAuth authentication
- Only the wants list owner can view or modify their lists
- Response format is XML or JSON (based on Accept header)
- Items in wants list are automatically matched with available articles on the marketplace
- Users receive notifications when matching articles become available
