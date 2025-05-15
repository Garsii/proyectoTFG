#include <Wire.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <Adafruit_PN532.h>

// Pines I2C y PN532
#define SDA_PIN     21
#define SCL_PIN     22
#define PN532_IRQ    2
#define PN532_RESET  3
Adafruit_PN532 nfc(PN532_IRQ, PN532_RESET);

// WiFi
const char* ssid     = "iPhone de Alvaro";
const char* password = "tfg2025ASIR";

// Señales
const int ledVerde = 16, ledRojo = 17, rele = 18;

// Parámetros I2C/NFC
const int I2C_RECOVER_THRESHOLD = 5;
const int MAX_I2C_ATTEMPTS      = 3;
const unsigned long NFC_COOLDOWN = 10000; // 10 s

// Estado
unsigned long lastReadTime = 0;
String lastUid = "";
int i2cErrorCount = 0;

// ——— Inicializa o recupera bus I2C + PN532 ———
void initI2CAndPN532() {
  Serial.println("🔄 Recover I2C & PN532");
  Wire.end();
  delay(50);
  Wire.begin(SDA_PIN, SCL_PIN);
  Wire.setClock(50000);
  nfc.begin();
  nfc.SAMConfig();
}

// ——— Conecta WiFi ———
void connectWiFi() {
  WiFi.disconnect(true);
  WiFi.begin(ssid, password);
  Serial.print("WiFi…");
  int tries = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    if (++tries > 20) {
      Serial.println("\nWiFi connect fail, restarting");
      ESP.restart();
    }
  }
  Serial.println(" OK, IP=" + WiFi.localIP().toString());
}

// ——— Setup ———
void setup() {
  Serial.begin(115200);
  pinMode(ledVerde, OUTPUT);
  pinMode(ledRojo, OUTPUT);
  pinMode(rele, OUTPUT);

  Wire.begin(SDA_PIN, SCL_PIN);
  Wire.setClock(50000);
  Serial.println("I2C a 50 kHz");

  connectWiFi();

  nfc.begin();
  if (!nfc.getFirmwareVersion()) {
    Serial.println("ERROR: PN532 no detectado");
    while (true) delay(1000);
  }
  nfc.SAMConfig();
  Serial.println("PN532 listo");
}

// ——— Lee UID con recuperación de bus ———
bool readNfcUid(String &outUid) {
  uint8_t buf[7], len;
  for (int attempt = 0; attempt < MAX_I2C_ATTEMPTS; ++attempt) {
    if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, buf, &len)) {
      outUid = "";
      char hex[3];
      for (int i = 0; i < len; i++) {
        sprintf(hex, "%02X", buf[i]);
        outUid += hex;
      }
      i2cErrorCount = 0;
      return true;
    }
    i2cErrorCount++;
    Serial.println("I2C NACK attempt " + String(attempt + 1));
    delay(50);
    if (i2cErrorCount >= I2C_RECOVER_THRESHOLD) {
      initI2CAndPN532();
      i2cErrorCount = 0;
    }
  }
  return false;
}

// ——— Envía al servidor SIEMPRE por HTTPS ———
void sendToServer(const String& uid) {
  if (WiFi.status() != WL_CONNECTED) connectWiFi();

  // Cliente seguro que acepta certificado autofirmado
  WiFiClientSecure client;
  client.setInsecure();  // << Confía en cualquier certificado (solo para dev)

  HTTPClient https;
  https.setTimeout(15000);

  // Usa la URL HTTPS directamente
  const char* url = "https://alvaroasir.com/api/registro-acceso";

  if (!https.begin(client, url)) {
    Serial.println("https.begin() falló");
    return;
  }

  https.addHeader("Content-Type", "application/json");
  String body = "{\"uid\":\"" + uid + "\",\"punto_acceso_id\":1}";

  int code = https.POST(body);
  Serial.println("HTTPS code: " + String(code));
  String resp = https.getString();
  Serial.println("Response: " + resp);

  if (code >= 200 && code < 300) {
    digitalWrite(ledVerde, HIGH);
    digitalWrite(rele, HIGH);
    delay(2000);
    digitalWrite(rele, LOW);
    digitalWrite(ledVerde, LOW);
  } else {
    Serial.println("Error: " + https.errorToString(code));
    digitalWrite(ledRojo, HIGH);
    delay(2000);
    digitalWrite(ledRojo, LOW);
  }

  https.end();
}

// ——— Loop ———
void loop() {
  String uid;
  if (readNfcUid(uid)) {
    if (uid != lastUid || millis() - lastReadTime > NFC_COOLDOWN) {
      lastUid = uid;
      lastReadTime = millis();
      Serial.println("UID: " + uid);
      sendToServer(uid);
    }
  }
  delay(100);
}
