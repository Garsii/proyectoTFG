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
const char* ssid     = "MIWIFI_xCTF";
const char* password = "4EGaxCT3";

// Señales
const int ledVerde = 16, ledRojo = 17, rele = 18;

// Parámetros
const int I2C_RECOVER_THRESHOLD = 5;   // tras tantos NACKs, reinit bus
const int MAX_I2C_ATTEMPTS      = 3;
const unsigned long NFC_COOLDOWN = 10000; // 10s por tarjeta

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
  WiFi.disconnect();
  WiFi.begin(ssid, password);
  Serial.print("WiFi…");
  int tries = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); Serial.print(".");
    if (++tries > 20) ESP.restart();
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
  Serial.println("I2C a 50 kHz");

  connectWiFi();

  nfc.begin();
  if (!nfc.getFirmwareVersion()) {
    Serial.println("ERROR: PN532 no detectado"); while(true) delay(1000);
  }
  nfc.SAMConfig();
  Serial.println("PN532 listo");
}

// ——— Lee UID con recuperación de bus ———
bool readNfcUid(String &outUid) {
  uint8_t buf[7], len;
  for (int attempt=0; attempt<MAX_I2C_ATTEMPTS; ++attempt) {
    if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, buf, &len)) {
      char hex[3];
      outUid = "";
      for (int i=0; i<len; i++){
        sprintf(hex, "%02X", buf[i]);
        outUid += hex;
      }
      i2cErrorCount = 0;
      return true;
    }
    // fallo I2C  
    i2cErrorCount++;
    Serial.println("I2C NACK attempt " + String(attempt+1));
    delay(50);
    if (i2cErrorCount >= I2C_RECOVER_THRESHOLD) {
      initI2CAndPN532();
      i2cErrorCount = 0;
    }
  }
  return false;
}

// ——— Envía al servidor ———
void sendToServer(const String& uid) {
  if (WiFi.status()!=WL_CONNECTED) connectWiFi();

  WiFiClientSecure client; client.setInsecure();
  HTTPClient http; http.setTimeout(10000);
  const char* url = "https://192.168.1.100/api/registro-acceso";

  if (!http.begin(client, url)) {
    Serial.println("http.begin() falló");
    return;
  }
  http.addHeader("Content-Type","application/x-www-form-urlencoded");
  String body = "uid=" + uid + "&punto_id=1";
  int code = http.POST(body);
  Serial.println("HTTP code: " + String(code));
  if (code==200) {
    digitalWrite(ledVerde,HIGH);
    digitalWrite(rele, HIGH);
    delay(2000);
    digitalWrite(rele, LOW);
    digitalWrite(ledVerde,LOW);
  } else {
    digitalWrite(ledRojo,HIGH);
    delay(2000);
    digitalWrite(ledRojo,LOW);
  }
  http.end();
}

// ——— Loop ———
void loop() {
  String uid;
  if (readNfcUid(uid)) {
    // cooldown
    if (uid != lastUid || millis()-lastReadTime > NFC_COOLDOWN) {
      lastUid = uid;
      lastReadTime = millis();
      Serial.println("UID: " + uid);
      sendToServer(uid);
    }
  }
  delay(100);
}
